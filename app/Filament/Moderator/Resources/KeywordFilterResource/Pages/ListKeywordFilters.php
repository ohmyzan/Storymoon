<?php

namespace App\Filament\Moderator\Resources\KeywordFilterResource\Pages;

use App\Filament\Moderator\Resources\KeywordFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKeywordFilters extends ListRecords
{
    protected static string $resource = KeywordFilterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
