<?php

namespace App\Events;

use App\Models\ChapterPurchase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChapterPurchased
{
  use Dispatchable, SerializesModels;

  public function __construct(
    public readonly ChapterPurchase $purchase,
  ) {}
}
