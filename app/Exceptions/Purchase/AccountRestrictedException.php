<?php

namespace App\Exceptions\Purchase;

use DomainException;

class AccountRestrictedException extends DomainException
{
  public function __construct()
  {
    parent::__construct(
      'Akun kamu sedang dibatasi dan tidak dapat melakukan transaksi.'
    );
  }
}
