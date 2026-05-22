<?php

namespace App\Filament\Admin\Resources\LegalContractResource\Pages;

use App\Filament\Admin\Resources\LegalContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLegalContracts extends ListRecords
{
    protected static string $resource = LegalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kosongkan agar Admin tidak bisa Create
        ];
    }
}
