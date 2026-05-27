<?php

namespace App\Listeners;

use App\Events\ChapterPurchased;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Update statistik novel setelah chapter dibeli.
 * Dijalankan di background agar tidak memperlambat transaksi.
 */
class UpdateNovelPurchaseStats implements ShouldQueue
{
  public int $tries = 3;

  public function handle(ChapterPurchased $event): void
  {
    $novel = $event->purchase->novel;

    // Increment counter langsung di DB — atomic, tidak ada race condition
    $novel->increment('total_purchases');

    Log::info('Novel purchase stats updated.', [
      'novel_id' => $novel->id,
    ]);
  }

  public function failed(ChapterPurchased $event, \Throwable $exception): void
  {
    Log::error('Failed to update novel purchase stats.', [
      'novel_id' => $event->purchase->novel_id,
      'error'    => $exception->getMessage(),
    ]);
  }
}
