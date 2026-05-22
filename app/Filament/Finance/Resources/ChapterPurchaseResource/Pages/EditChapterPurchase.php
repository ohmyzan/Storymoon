<?php

namespace App\Filament\Finance\Resources\ChapterPurchaseResource\Pages;

use App\Filament\Finance\Resources\ChapterPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChapterPurchase extends EditRecord
{
    protected static string $resource = ChapterPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
