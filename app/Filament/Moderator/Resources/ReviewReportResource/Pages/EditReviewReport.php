<?php

namespace App\Filament\Moderator\Resources\ReviewReportResource\Pages;

use App\Filament\Moderator\Resources\ReviewReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; // Tambahkan ini

class EditReviewReport extends EditRecord
{
    protected static string $resource = ReviewReportResource::class;

    public ?string $newStatus = null;
    public ?string $moderatorNotes = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->newStatus = $data['status'] ?? null;
        $this->moderatorNotes = $data['moderator_notes'] ?? null;
        return $data;
    }

    // 🌟 FIX DARI CLAUDE: Eksekusi fungsi Model
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();

        if ($this->newStatus) {
            match ($this->newStatus) {
                'resolved'  => $record->resolve($user, $this->moderatorNotes),
                'rejected'  => $record->reject($user, $this->moderatorNotes),
                // Asumsi method escalate() ada di Model ReviewReport. Jika tidak ada, gunakan update biasa:
                'escalated' => tap($record)->update([
                    'status' => 'escalated',
                    'handled_by' => $user->id,
                    'moderator_notes' => $this->moderatorNotes
                ]),
                default     => null,
            };

            // Hapus ulasan jika terbukti melanggar
            if ($this->newStatus === 'resolved' && $record->review) {
                $record->review->delete();
            }
        }
        return $record;
    }
}
