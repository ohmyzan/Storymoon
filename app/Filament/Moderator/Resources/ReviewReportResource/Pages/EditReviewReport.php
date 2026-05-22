<?php

namespace App\Filament\Moderator\Resources\ReviewReportResource\Pages;

use App\Filament\Moderator\Resources\ReviewReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditReviewReport extends EditRecord
{
    protected static string $resource = ReviewReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['handled_by'] = Auth::id();
        return $data;
    }

    protected function afterSave(): void
    {
        $review = $this->record->review;

        // Jika terbukti melanggar (Resolved), hapus ulasannya!
        if ($this->record->status === 'resolved' && $review) {
            // Jika tabel reviews Anda punya softDeletes, ini akan me-nonaktifkannya dengan aman
            // Jika tidak, ini akan menghapusnya secara permanen.
            $review->delete();
        }
    }
}
