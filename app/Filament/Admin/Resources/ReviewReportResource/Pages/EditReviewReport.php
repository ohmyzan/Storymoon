<?php

namespace App\Filament\Admin\Resources\ReviewReportResource\Pages;

use App\Filament\Admin\Resources\ReviewReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditReviewReport extends EditRecord
{
    protected static string $resource = ReviewReportResource::class;

    public bool $shouldBanUser = false;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['handled_by'] = Auth::id();

        if (isset($data['ban_user']) && $data['ban_user'] === true) {
            $this->shouldBanUser = true;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $review = $this->record->review;

        if ($this->shouldBanUser && $review && $review->user) {
            // [FIX] Menggunakan method ban() alih-alih mass assignment
            $review->user->ban();
        }

        if ($this->record->status === 'resolved' && $review) {
            $review->delete();
        }
    }
}
