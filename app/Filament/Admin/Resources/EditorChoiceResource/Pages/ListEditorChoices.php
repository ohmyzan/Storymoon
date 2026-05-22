<?php

namespace App\Filament\Admin\Resources\EditorChoiceResource\Pages;

use App\Filament\Admin\Resources\EditorChoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEditorChoices extends ListRecords
{
    protected static string $resource = EditorChoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kosongkan array ini. Hapus Actions\CreateAction::make()
            // Agar Admin tidak bisa membuat nominasi manual.
        ];
    }
}
