<?php

namespace App\Filament\Editor\Resources\TextReviewResource\Pages;

use App\Filament\Editor\Resources\TextReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditTextReview extends EditRecord
{
    protected static string $resource = TextReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Hapus DeleteAction untuk menjaga rekam jejak
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 🌟 LOGIKA PROGRESSIVE ONBOARDING
        if ($data['status'] === 'approved') {

            // Catat siapa editor yang bertanggung jawab meloloskan naskah ini
            $data['editor_id'] = Auth::id();

            // Jika Eksklusif: Lempar ke penulis untuk isi form KTP
            if ($this->record->contract_type === 'exclusive') {
                $data['status'] = 'kyc_submission';
            }
            // Jika Non-Eksklusif: Langsung Sah & Aktif tanpa KTP!
            else {
                $data['status'] = 'active';
                $data['signed_at'] = now();
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Jika kontrak berjalan (baik lanjut ke KYC maupun langsung Aktif), 
        // ikat Editor ini ke Novel tersebut sebagai Supervisor selamanya.
        if (in_array($this->record->status, ['kyc_submission', 'active'])) {
            DB::transaction(function () {
                $this->record->novel->update([
                    'editor_id' => Auth::id(),
                ]);
            });
        }
    }
}
