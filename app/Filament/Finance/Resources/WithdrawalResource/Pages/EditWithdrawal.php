<?php

namespace App\Filament\Finance\Resources\WithdrawalResource\Pages;

use App\Filament\Finance\Resources\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditWithdrawal extends EditRecord
{
    protected static string $resource = WithdrawalResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['processed_by'] = Auth::id();
        return $data;
    }

    protected function afterSave(): void
    {
        // Logika Pengembalian Dana (Refund) jika penarikan ditolak
        if ($this->record->status === 'rejected') {

            DB::transaction(function () {
                $user = $this->record->user;

                // 🌟 PERBAIKAN: Kembalikan koin ke dompet (Wallet)
                $user->wallet->increment('coin_balance', $this->record->coins_redeemed);
            });
        }
    }
}
