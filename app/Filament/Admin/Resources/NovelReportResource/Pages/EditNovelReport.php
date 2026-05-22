<?php

namespace App\Filament\Admin\Resources\NovelReportResource\Pages;

use App\Filament\Admin\Resources\NovelReportResource;
use Filament\Resources\Pages\EditRecord;

class EditNovelReport extends EditRecord
{
    protected static string $resource = NovelReportResource::class;

    protected function afterSave(): void
    {
        $novel = $this->record->novel;
        $isTakedown = $this->data['takedown_novel'] ?? false;

        // 🌟 PERBAIKAN: Gunakan kolom is_frozen untuk membekukan novel
        if ($isTakedown && $novel) {
            $novel->update([
                'is_frozen' => true,
            ]);
        }
    }
}
