<?php

namespace App\Filament\Admin\Resources\NovelReportResource\Pages;

use App\Filament\Admin\Resources\NovelReportResource;
use Filament\Resources\Pages\EditRecord;

class EditNovelReport extends EditRecord
{
    protected static string $resource = NovelReportResource::class;

    // 🌟 1. Siapkan penampung yang aman dari pembersihan (dehydration)
    public bool $shouldTakedown = false;

    // 🌟 2. Tangkap nilai toggle SEBELUM data dibuang
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['takedown_novel']) && $data['takedown_novel'] === true) {
            $this->shouldTakedown = true;
        }
        return $data;
    }

    protected function afterSave(): void
    {
        $novel = $this->record->novel;

        if ($this->shouldTakedown && $novel) {
            // [FIX] Menggunakan transitionTo sesuai arsitektur Model terbaru
            $novel->transitionTo('frozen');
        }
    }
}
