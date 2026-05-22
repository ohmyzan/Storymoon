<?php

namespace App\Filament\Finance\Resources\WithdrawalResource\Pages;

use App\Filament\Finance\Resources\WithdrawalResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
