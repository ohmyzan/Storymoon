<?php

namespace App\Exceptions\Purchase;

use DomainException;

class InsufficientCoinsException extends DomainException
{
  public function __construct(
    public readonly int $required,
    public readonly int $available,
  ) {
    parent::__construct(
      "Saldo koin tidak mencukupi. Dibutuhkan: {$required}, tersedia: {$available}."
    );
  }
}
