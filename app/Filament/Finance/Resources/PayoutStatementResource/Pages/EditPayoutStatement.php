<?php

namespace App\Filament\Finance\Resources\PayoutStatementResource\Pages;

use App\Filament\Finance\Resources\PayoutStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditPayoutStatement extends EditRecord
{
    protected static string $resource = PayoutStatementResource::class;

    public ?string $newStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Tangkap status baru dari form
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->newStatus = $data['status'] ?? null;
        return $data;
    }

    // 🔥 CORE LOGIC (RECOMMENDED FILAMENT WAY)
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldStatus = $record->status;

        DB::transaction(function () use ($record, $oldStatus, $data) {

            // ✅ Jika berubah ke credited_to_wallet
            if ($oldStatus !== 'credited_to_wallet' && $this->newStatus === 'credited_to_wallet') {

                // 1. Inject saldo
                $record->author->wallet->increment(
                    'coin_balance',
                    $record->net_author_coins
                );

                // 2. Tandai via Model (single source of truth)
                $record->markAsCredited();
            }

            // ❗ Optional: handle gagal
            if ($this->newStatus === 'failed') {
                $record->markAsFailed();
            }

            // ✅ Tetap update field lain dari form
            $record->update($data);
        });

        return $record->fresh(); // ambil state terbaru
    }
}
