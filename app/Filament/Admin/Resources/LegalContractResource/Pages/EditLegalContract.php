<?php

namespace App\Filament\Admin\Resources\LegalContractResource\Pages;

use App\Filament\Admin\Resources\LegalContractResource;
use Filament\Resources\Pages\EditRecord;

class EditLegalContract extends EditRecord
{
    protected static string $resource = LegalContractResource::class;

    public ?string $newStatus = null;
    public ?string $editorNotes = null;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['status'])) {
            $this->newStatus = $data['status'];
        }
        if (isset($data['editor_notes'])) {
            $this->editorNotes = $data['editor_notes'];
        }
        return $data;
    }

    protected function afterSave(): void
    {
        // [FIX] Panggil method mutlak dari model Contract
        if ($this->newStatus && $this->newStatus !== $this->record->status) {
            $this->record->advanceTo($this->newStatus, $this->editorNotes);
        }
    }
}
