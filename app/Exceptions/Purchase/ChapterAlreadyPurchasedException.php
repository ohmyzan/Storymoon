<?php

namespace App\Exceptions\Purchase;

use DomainException;

class ChapterAlreadyPurchasedException extends DomainException
{
  public function __construct(string $chapterId)
  {
    parent::__construct(
      "Chapter {$chapterId} sudah dibeli sebelumnya."
    );
  }
}
