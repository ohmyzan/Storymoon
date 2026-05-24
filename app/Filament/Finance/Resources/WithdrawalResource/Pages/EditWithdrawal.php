<?php

namespace App\Filament\Finance\Resources\WithdrawalResource\Pages;

use App\Filament\Finance\Resources\WithdrawalResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; // Tambahkan ini
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditWithdrawal extends EditRecord
{
    protected static string $resource = WithdrawalResource::class;

    public ?string $newStatus = null;
    public ?string $proofImage = null;
    public ?string $financeNotes = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Tangkap semua input non-fillable
        $this->newStatus = $data['status'] ?? null;
        $this->proofImage = $data['proof_image'] ?? null;
        $this->financeNotes = $data['finance_notes'] ?? null;

        return $data;
    }

    // 🌟 FIX: Eksekusi Method Approve & Reject mutlak dari Model
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $financeStaff = auth()->user();

        // Cegah eksekusi ganda jika status sudah approved/rejected sebelumnya
        if ($record->status === 'pending') {

            if ($this->newStatus === 'approved') {
                // Eksekusi sukses (Bukti transfer diwajibkan oleh method)
                $record->approve($financeStaff, $this->proofImage ?? '');
            } elseif ($this->newStatus === 'rejected') {
                // Eksekusi Refund
                DB::transaction(function () use ($record, $financeStaff) {
                    // 1. Kembalikan koin yang ditarik ke dompet
                    $record->user->wallet->increment('coin_balance', $record->coins_redeemed);

                    // 2. Tandai ditolak
                    $record->reject($financeStaff, $this->financeNotes ?? '');
                });
            }
        }

        return $record;
    }
}
