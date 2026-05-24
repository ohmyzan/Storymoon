<?php

namespace App\Filament\Admin\Resources\CommentReportResource\Pages;

use App\Filament\Admin\Resources\CommentReportResource;
use Filament\Resources\Pages\EditRecord;

class EditCommentReport extends EditRecord
{
    protected static string $resource = CommentReportResource::class;

    public bool $shouldBanUser = false;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['ban_user']) && $data['ban_user'] === true) {
            $this->shouldBanUser = true;
        }
        return $data;
    }

    protected function afterSave(): void
    {
        $comment = $this->record->comment;

        if ($this->shouldBanUser && $comment && $comment->user) {
            // [FIX] Menggunakan method ban() bawaan model User sesuai audit
            $comment->user->ban();
        }

        // Jika kasus diselesaikan, sembunyikan komentar otomatis
        if ($this->record->status === 'resolved' && $comment) {
            $comment->update(['is_hidden' => true]);
        }
    }
}
