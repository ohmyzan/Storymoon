<?php

namespace App\Services;

use App\Contracts\PurchaseServiceInterface;
use App\Events\ChapterPurchased;
use App\Exceptions\Purchase\AccountRestrictedException;
use App\Exceptions\Purchase\ChapterAlreadyPurchasedException;
use App\Exceptions\Purchase\ChapterNotAvailableException;
use App\Exceptions\Purchase\InsufficientCoinsException;
use App\Models\Chapter;
use App\Models\ChapterPurchase;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Support\SettingsCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoinPurchaseService implements PurchaseServiceInterface
{
    // =========================================================
    // ENTRY POINT UTAMA
    // =========================================================

  /**
   * Reader membeli chapter premium.
   *
   * Flow:
   * 1. Validasi bisnis (sebelum DB lock — efisien)
   * 2. Lock wallet reader (cegah race condition)
   * 3. Validasi saldo di dalam lock
   * 4. Hitung bagi hasil berdasarkan kontrak aktif novel
   * 5. Debit koin reader, credit revenue author
   * 6. Catat ChapterPurchase (ledger immutable)
   * 7. Catat Transaction audit trail (reader & author)
   * 8. Fire event → Listeners handle side effects
   */
  public function purchase(User $reader, Chapter $chapter): ChapterPurchase
  {
    // Pastikan relasi sudah di-load sebelum masuk transaction
    // (eager load harus dilakukan di controller sebelum memanggil service)
    $chapter->loadMissing('novel.activeContract');

    // Validasi awal — sebelum DB lock agar lebih efisien
    $this->validatePurchase($reader, $chapter);

    return DB::transaction(function () use ($reader, $chapter) {

      // Lock wallet reader — cegah double-spend
      $readerWallet = Wallet::where('user_id', $reader->id)
        ->lockForUpdate()
        ->firstOrFail();

      // Validasi saldo di dalam lock (nilai sudah terkunci)
      if (! $readerWallet->hasSufficientCoins($chapter->coin_price)) {
        throw new InsufficientCoinsException(
          $chapter->coin_price,
          $readerWallet->coin_balance,
        );
      }

      $settings = SettingsCache::get();

      // Hitung bagi hasil
      [$authorEarning, $platformEarning, $revenueSharePct, $contractType]
        = $this->calculateEarnings($chapter, $settings);

      // Lock wallet author
      $authorWallet = Wallet::where('user_id', $chapter->novel->author_id)
        ->lockForUpdate()
        ->firstOrFail();

      $coinPriceRupiah     = $settings['coin_price'] ?? 100;
      $authorRevenueRupiah = $authorEarning * $coinPriceRupiah;

      // Mutasi saldo
      $readerWallet->debitCoins($chapter->coin_price);
      $authorWallet->creditRevenue($authorRevenueRupiah);

      // Catat pembelian — ledger immutable
      $purchase = ChapterPurchase::create([
        'reader_id'              => $reader->id,
        'chapter_id'             => $chapter->id,
        'novel_id'               => $chapter->novel_id,
        'author_id'              => $chapter->novel->author_id,
        'coin_price'             => $chapter->coin_price,
        'author_earning'         => $authorEarning,
        'platform_earning'       => $platformEarning,
        'contract_type_snapshot' => $contractType,
        'revenue_share_snapshot' => $revenueSharePct,
      ]);

      // Audit trail reader
      Transaction::create([
        'user_id'       => $reader->id,
        'wallet_id'     => $readerWallet->id,
        'type'          => 'chapter_purchase',
        'coin_amount'   => $chapter->coin_price,
        'rupiah_amount' => $chapter->coin_price * $coinPriceRupiah,
        'status'        => 'completed',
        'reference_id'  => $purchase->id,
        'meta_data'     => [
          'chapter_id'  => $chapter->id,
          'novel_id'    => $chapter->novel_id,
          'novel_title' => $chapter->novel->title,
        ],
      ]);

      // Audit trail author
      Transaction::create([
        'user_id'       => $chapter->novel->author_id,
        'wallet_id'     => $authorWallet->id,
        'type'          => 'revenue_share',
        'coin_amount'   => $authorEarning,
        'rupiah_amount' => $authorRevenueRupiah,
        'status'        => 'completed',
        'reference_id'  => $purchase->id,
        'meta_data'     => [
          'chapter_id'      => $chapter->id,
          'reader_id'       => $reader->id,
          'revenue_share_%' => $revenueSharePct,
        ],
      ]);

      Log::info('ChapterPurchase completed.', [
        'purchase_id' => $purchase->id,
        'reader_id'   => $reader->id,
        'chapter_id'  => $chapter->id,
        'coin_price'  => $chapter->coin_price,
        'author_earn' => $authorEarning,
      ]);

      // Fire event — side effects ditangani Listeners secara async
      ChapterPurchased::dispatch($purchase);

      return $purchase;
    });
  }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

  /**
   * Validasi bisnis sebelum DB lock.
   * Urutan: yang paling murah (tidak query DB) divalidasi duluan.
   */
  private function validatePurchase(User $reader, Chapter $chapter): void
  {
    // 1. Cek status akun reader (no DB query)
    if ($reader->isBanned() || $reader->isSuspended()) {
      throw new AccountRestrictedException();
    }

    // 2. Cek chapter tersedia (no DB query — sudah di-load)
    if (! $chapter->is_premium) {
      throw new ChapterNotAvailableException($chapter->id);
    }

    if ($chapter->status !== 'published') {
      throw new ChapterNotAvailableException($chapter->id);
    }

    // 3. Cek double purchase (1 DB query)
    $alreadyPurchased = ChapterPurchase::where('reader_id', $reader->id)
      ->where('chapter_id', $chapter->id)
      ->exists();

    if ($alreadyPurchased) {
      throw new ChapterAlreadyPurchasedException($chapter->id);
    }
  }

  /**
   * Hitung bagi hasil author vs platform.
   *
   * Prioritas:
   * 1. Kontrak aktif novel → revenue_share dari kontrak
   * 2. Tidak ada kontrak → fallback GeneralSettings
   *
   * @return array{int, int, int, string}
   *         [authorEarning, platformEarning, revenueSharePct, contractType]
   */
  private function calculateEarnings(Chapter $chapter, array $settings): array
  {
    $novel          = $chapter->novel;
    $activeContract = $novel->activeContract;
    $coinPrice      = $chapter->coin_price;

    if ($activeContract) {
      $contractType    = $activeContract->type;
      $revenueSharePct = $activeContract->revenue_share_percentage;
    } else {
      $contractType    = 'non_exclusive';
      $revenueSharePct = $settings['revenue_share_non_exclusive'] ?? 50;
    }

    $authorEarning   = (int) round($coinPrice * $revenueSharePct / 100);
    $platformEarning = $coinPrice - $authorEarning;

    return [$authorEarning, $platformEarning, $revenueSharePct, $contractType];
  }
}
