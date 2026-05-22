<?php

namespace App\Filament\Admin\Resources\CommentReportResource\Pages;

use App\Filament\Admin\Resources\CommentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommentReports extends ListRecords
{
    protected static string $resource = CommentReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
