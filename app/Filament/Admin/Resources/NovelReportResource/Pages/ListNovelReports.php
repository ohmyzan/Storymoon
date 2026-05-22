<?php

namespace App\Filament\Admin\Resources\NovelReportResource\Pages;

use App\Filament\Admin\Resources\NovelReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNovelReports extends ListRecords
{
    protected static string $resource = NovelReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
