<?php

namespace App\Filament\Moderator\Resources\KeywordFilterResource\Pages;

use App\Filament\Moderator\Resources\KeywordFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateKeywordFilter extends CreateRecord
{
    protected static string $resource = KeywordFilterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Otomatis merekam ID Moderator yang memasukkan kata ini
        $data['created_by'] = Auth::id();

        return $data;
    }
}
