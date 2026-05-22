<?php

namespace App\Filament\Finance\Resources\PayoutStatementResource\Pages;

use App\Filament\Finance\Resources\PayoutStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayoutStatement extends CreateRecord
{
    protected static string $resource = PayoutStatementResource::class;
}
