<?php

namespace App\Filament\Finance\Resources\TopUpResource\Pages;

use App\Filament\Finance\Resources\TopUpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTopUps extends ListRecords
{
    protected static string $resource = TopUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
