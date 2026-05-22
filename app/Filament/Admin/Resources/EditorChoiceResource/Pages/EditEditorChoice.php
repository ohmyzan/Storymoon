<?php

namespace App\Filament\Admin\Resources\EditorChoiceResource\Pages;

use App\Filament\Admin\Resources\EditorChoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEditorChoice extends EditRecord
{
    protected static string $resource = EditorChoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
