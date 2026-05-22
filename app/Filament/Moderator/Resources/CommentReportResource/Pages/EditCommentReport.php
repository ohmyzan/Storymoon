<?php

namespace App\Filament\Moderator\Resources\CommentReportResource\Pages;

use App\Filament\Moderator\Resources\CommentReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCommentReport extends EditRecord
{
    protected static string $resource = CommentReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Catat siapa Moderator yang mengeksekusi
        $data['handled_by'] = Auth::id();
        return $data;
    }

    protected function afterSave(): void
    {
        // Ambil komentar terkait
        $comment = $this->record->comment;

        // Jika laporan divalidasi (Resolved) -> Otomatis Sembunyikan Komentar (is_hidden = true)
        if ($this->record->status === 'resolved' && $comment) {
            $comment->update(['is_hidden' => true]);
        }

        // Jika laporan ditolak (Rejected) -> Pastikan komentar tetap muncul (is_hidden = false)
        if ($this->record->status === 'rejected' && $comment) {
            $comment->update(['is_hidden' => false]);
        }
    }
}
