<?php

namespace App\Filament\Finance\Resources\ChapterPurchaseResource\Pages;

use App\Filament\Finance\Resources\ChapterPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChapterPurchases extends ListRecords
{
    protected static string $resource = ChapterPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
