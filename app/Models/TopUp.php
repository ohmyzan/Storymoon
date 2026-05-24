<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TopUp extends Model
{
    use HasFactory, HasUlids, LogsActivity;

    protected $fillable = [
        'user_id',
        'reference_id',
        'amount_rupiah',
        'coins_granted',
        'payment_method',
        // [FIX] Tambah gateway_payload dari migrasi
        'gateway_payload',
        'settled_at',
        // [NOTE] 'status' tidak di $fillable
        // Status hanya boleh diubah oleh MidtransWebhookHandler
    ];

    protected $casts = [
        'settled_at'      => 'datetime',
        'amount_rupiah'   => 'integer',
        'coins_granted'   => 'integer',
        // [FIX] Tambah cast untuk gateway_payload (json → array)
        'gateway_payload' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'reference_id', 'amount_rupiah', 'coins_granted', 'status', 'settled_at'])
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

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccess(Builder $query): Builder
    {
        return $query->where('status', 'success');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Tandai top-up berhasil — hanya dipanggil dari MidtransWebhookHandler
     */
    public function markAsSuccess(array $gatewayPayload): void
    {
        $this->status          = 'success';
        $this->settled_at      = now();
        $this->gateway_payload = $gatewayPayload;
        $this->save();
    }

    public function markAsFailed(array $gatewayPayload): void
    {
        $this->status          = 'failed';
        $this->gateway_payload = $gatewayPayload;
        $this->save();
    }

    public function markAsExpired(): void
    {
        $this->status = 'expired';
        $this->save();
    }
}
