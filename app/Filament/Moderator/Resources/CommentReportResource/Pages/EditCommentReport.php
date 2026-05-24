<?php

namespace App\Filament\Moderator\Resources\CommentReportResource\Pages;

use App\Filament\Moderator\Resources\CommentReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; // Tambahkan ini

class EditCommentReport extends EditRecord
{
    protected static string $resource = CommentReportResource::class;

    public ?string $newStatus = null;
    public ?string $moderatorNotes = null;

    // Tangkap input dari form sebelum dibuang karena dehydrated(false)
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->newStatus = $data['status'] ?? null;
        $this->moderatorNotes = $data['moderator_notes'] ?? null;
        return $data;
    }

    // 🌟 FIX DARI CLAUDE: Eksekusi eksplisit memanggil method Model
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();

        if ($this->newStatus) {
            match ($this->newStatus) {
                'resolved'  => $record->resolve($user, $this->moderatorNotes),
                'rejected'  => $record->reject($user, $this->moderatorNotes),
                'escalated' => $record->escalate($user, $this->moderatorNotes),
                default     => null,
            };

            // Logika sembunyikan komentar ditaruh di sini
            if ($this->newStatus === 'resolved' && $record->comment) {
                $record->comment->update(['is_hidden' => true]);
            }
        }
        return $record;
    }
}
