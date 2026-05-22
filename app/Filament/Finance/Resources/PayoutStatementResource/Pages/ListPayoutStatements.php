<?php

namespace App\Filament\Finance\Resources\PayoutStatementResource\Pages;

use App\Filament\Finance\Resources\PayoutStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayoutStatements extends ListRecords
{
    protected static string $resource = PayoutStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
