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
        if ($this->shouldBanUser) {
            $comment = $this->record->comment;

            if ($comment && $comment->user) {
                // 🌟 Menggunakan banned_at yang sudah sah!
                $comment->user->update(['banned_at' => now()]);
                $comment->update(['is_hidden' => true]);
            }
        }
    }
}
