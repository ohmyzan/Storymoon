<?php

namespace App\Filament\Admin\Resources\LegalContractResource\Pages;

use App\Filament\Admin\Resources\LegalContractResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLegalContract extends EditRecord
{
    protected static string $resource = LegalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Hapus DeleteAction untuk menjaga keutuhan data hukum
        ];
    }
}
