<?php

namespace App\Exceptions\Purchase;

use DomainException;

class ChapterNotAvailableException extends DomainException
{
  public function __construct(string $chapterId)
  {
    parent::__construct(
      "Chapter {$chapterId} tidak tersedia untuk dibeli saat ini."
    );
  }
}
