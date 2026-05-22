<?php

namespace App\Filament\Finance\Resources\TopUpResource\Pages;

use App\Filament\Finance\Resources\TopUpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditTopUp extends EditRecord
{
    protected static string $resource = TopUpResource::class;

    protected function beforeSave(): void
    {
        $oldStatus = $this->record->getOriginal('status');
        $newStatus = $this->data['status'];

        // Cek transisi: Jika status SEBELUMNYA BUKAN success dan DIUBAH MENJADI success
        if ($oldStatus !== 'success' && $newStatus === 'success') {

            // 🌟 BUNGKUS DENGAN DB::transaction (Keamanan Finansial Mutlak)
            DB::transaction(function () {
                $user = $this->record->user;

                // 🌟 PERBAIKAN: Arahkan ke relasi dompet (Wallet)
                $user->wallet->increment('coin_balance', $this->record->coins_granted);

                // Catat waktu sukses transaksi
                $this->record->settled_at = now();

                // Opsional tingkat lanjut: Anda bisa meng-create data ke tabel 'transactions' di sini
            });
        }
    }
}
