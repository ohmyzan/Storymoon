<?php

namespace App\Filament\Moderator\Resources\KeywordFilterResource\Pages;

use App\Filament\Moderator\Resources\KeywordFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKeywordFilter extends EditRecord
{
    protected static string $resource = KeywordFilterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
