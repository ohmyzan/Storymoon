<?php

namespace App\Listeners;

use App\Events\ChapterPurchased;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * ShouldQueue → listener ini dijalankan di background job.
 * Notifikasi tidak boleh memperlambat response transaksi utama.
 */
class NotifyAuthorOnPurchase implements ShouldQueue
{
  // Jika job gagal, coba lagi maksimal 3x
  public int $tries = 3;

  public function handle(ChapterPurchased $event): void
  {
    $purchase = $event->purchase;
    $author   = $purchase->author;

    // TODO: Ganti dengan Notification class saat sudah dibuat
    // $author->notify(new ChapterPurchasedNotification($purchase));

    Log::info('Author notified of chapter purchase.', [
      'author_id'   => $author->id,
      'purchase_id' => $purchase->id,
      'earning'     => $purchase->author_earning,
    ]);
  }

  /**
   * Jika job gagal setelah semua retry habis — log error tanpa crash app.
   */
  public function failed(ChapterPurchased $event, \Throwable $exception): void
  {
    Log::error('Failed to notify author on purchase.', [
      'purchase_id' => $event->purchase->id,
      'error'       => $exception->getMessage(),
    ]);
  }
}
