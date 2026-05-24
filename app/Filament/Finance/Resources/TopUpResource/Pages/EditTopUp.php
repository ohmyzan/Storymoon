<?php

namespace App\Filament\Finance\Resources\TopUpResource\Pages;

use App\Filament\Finance\Resources\TopUpResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; // Tambahkan ini
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditTopUp extends EditRecord
{
    protected static string $resource = TopUpResource::class;

    public ?string $newStatus = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->newStatus = $data['status'] ?? null;
        return $data;
    }

    // 🌟 FIX: Gunakan handleRecordUpdate
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldStatus = $record->status;

        // Validasi Ketat: Hanya proses jika sebelumnya BUKAN success
        if ($oldStatus !== 'success' && $this->newStatus === 'success') {
            DB::transaction(function () use ($record) {
                // 1. Tambah saldo pembaca
                $record->user->wallet->increment('coin_balance', $record->coins_granted);

                // 2. Panggil method Model (Gunakan array kosong karena ini verifikasi manual Admin, bukan dari Midtrans)
                $record->markAsSuccess([
                    'manual_verification_by_admin' => Auth::user()?->id
                ]);
            });
        } elseif ($oldStatus !== 'failed' && $this->newStatus === 'failed') {
            $record->markAsFailed(['reason' => 'Manually failed by admin']);
        } elseif ($oldStatus !== 'expired' && $this->newStatus === 'expired') {
            $record->markAsExpired();
        }

        return $record;
    }
}
