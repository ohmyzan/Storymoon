<?php

namespace App\Filament\Finance\Resources\PayoutStatementResource\Pages;

use App\Filament\Finance\Resources\PayoutStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditPayoutStatement extends EditRecord
{
    protected static string $resource = PayoutStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // 🌟 TAMBAHKAN LOGIKA INJEKSI DANA INI
    protected function beforeSave(): void
    {
        $oldStatus = $this->record->getOriginal('status');
        $newStatus = $this->data['status'];

        if ($oldStatus !== 'credited_to_wallet' && $newStatus === 'credited_to_wallet') {

            DB::transaction(function () {
                // Berdasarkan model Anda, relasinya bernama 'author'
                $author = $this->record->author;

                // Suntikkan pendapatan bersih (Netto) ke dompet
                $author->wallet->increment('coin_balance', $this->record->net_author_coins);
            });
        }
    }
}
