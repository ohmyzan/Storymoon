<?php

namespace App\Filament\Admin\Resources\ReviewReportResource\Pages;

use App\Filament\Admin\Resources\ReviewReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditReviewReport extends EditRecord
{
    protected static string $resource = ReviewReportResource::class;

    // 🌟 1. Variabel penampung
    public bool $shouldBanUser = false;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Catat siapa Admin yang mengeksekusi
        $data['handled_by'] = Auth::id();

        // 🌟 2. Tangkap nilai toggle SEBELUM dibuang oleh Filament
        if (isset($data['ban_user']) && $data['ban_user'] === true) {
            $this->shouldBanUser = true;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $review = $this->record->review;

        // 🌟 3. Eksekusi HUKUMAN BAN TERLEBIH DAHULU (sebelum review dihapus)
        if ($this->shouldBanUser && $review && $review->user) {
            $review->user->update([
                'banned_at' => now()
            ]);
        }

        // 🌟 4. Bersihkan ulasan sampah jika kasus ditutup (resolved)
        if ($this->record->status === 'resolved' && $review) {
            $review->delete();
        }
    }
}
