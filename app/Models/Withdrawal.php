<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Withdrawal extends Model
{
    use HasFactory, HasUlids, LogsActivity;

    protected $fillable = [
        'user_id',
        'coins_redeemed',
        'amount_rupiah',
        // Snapshot rekening bank saat pengajuan
        'bank_name',
        'bank_account_number', // Dienkripsi via $casts
        'bank_account_name',
        // [FIX] Hapus: 'status', 'finance_notes', 'processed_by', 'proof_image'
        // Kolom finance hanya boleh diubah eksplisit oleh WithdrawalService
    ];

    protected $casts = [
        'coins_redeemed' => 'integer',
        'amount_rupiah'  => 'integer',
        // [FIX] Enkripsi nomor rekening at-rest — sesuai rekomendasi audit migrasi (UU PDP)
        'bank_account_number' => 'encrypted',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                // [NOTE] Jangan log bank_account_number yang terenkripsi
                'user_id',
                'coins_redeemed',
                'amount_rupiah',
                'bank_name',
                'status',
                'processed_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Approve withdrawal — hanya dipanggil dari WithdrawalService
     */
    public function approve(User $financeStaff, string $proofImagePath): void
    {
        $this->status       = 'approved';
        $this->processed_by = $financeStaff->id;
        $this->proof_image  = $proofImagePath;
        $this->save();
    }

    public function reject(User $financeStaff, string $notes): void
    {
        $this->status        = 'rejected';
        $this->processed_by  = $financeStaff->id;
        $this->finance_notes = $notes;
        $this->save();
    }
}
