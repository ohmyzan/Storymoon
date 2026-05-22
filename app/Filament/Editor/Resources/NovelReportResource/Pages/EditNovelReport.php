<?php

namespace App\Filament\Editor\Resources\NovelReportResource\Pages;

use App\Filament\Editor\Resources\NovelReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditNovelReport extends EditRecord
{
    protected static string $resource = NovelReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Otomatis mencatat ID Editor yang sedang menangani laporan ini
        $data['handled_by'] = Auth::id();

        return $data;
    }
}
