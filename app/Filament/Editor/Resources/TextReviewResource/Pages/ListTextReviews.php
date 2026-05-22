<?php

namespace App\Filament\Editor\Resources\TextReviewResource\Pages;

use App\Filament\Editor\Resources\TextReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTextReviews extends ListRecords
{
    protected static string $resource = TextReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
