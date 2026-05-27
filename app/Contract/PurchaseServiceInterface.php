<?php

namespace App\Contracts;

use App\Models\Chapter;
use App\Models\ChapterPurchase;
use App\Models\User;

interface PurchaseServiceInterface
{
  /**
   * Reader membeli chapter premium dengan koin.
   *
   * @throws \App\Exceptions\Purchase\InsufficientCoinsException
   * @throws \App\Exceptions\Purchase\ChapterAlreadyPurchasedException
   * @throws \App\Exceptions\Purchase\ChapterNotAvailableException
   * @throws \App\Exceptions\Purchase\AccountRestrictedException
   */
  public function purchase(User $reader, Chapter $chapter): ChapterPurchase;
}
