<?php

namespace App\Filament\Editor\Resources\TextReviewResource\Pages;

use App\Filament\Editor\Resources\TextReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class EditTextReview extends EditRecord
{
    protected static string $resource = TextReviewResource::class;

    // 🌟 FIX: Jangan gunakan mutateFormDataBeforeSave untuk mengubah status yang sudah dilarang di $fillable.
    // Kita tangkap secara manual dan eksekusi via method Model di afterSave.

    public ?string $newStatus = null;
    public ?string $editorNotes = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Tangkap data dari form, jangan dikembalikan ke array save default
        $this->newStatus = $data['status'] ?? null;
        $this->editorNotes = $data['editor_notes'] ?? null;

        return $data;
    }

    protected function afterSave(): void
    {
        $contract = $this->record;

        // Eksekusi logika status
        if ($this->newStatus) {
            if ($this->newStatus === 'approved') {
                $statusToAdvance = ($contract->contract_type === 'exclusive') ? 'kyc_submission' : 'active';
                $contract->advanceTo($statusToAdvance, $this->editorNotes);
            } else {
                // Untuk revision_needed atau rejected
                $contract->advanceTo($this->newStatus, $this->editorNotes);
            }
        }

        // Jika kontrak berjalan, ikat Editor ini ke Novel
        if (in_array($contract->status, ['kyc_submission', 'active'])) {
            DB::transaction(function () use ($contract) {
                $contract->novel->update([
                    'editor_id' => Auth::id(),
                ]);
            });
        }
    }
}
