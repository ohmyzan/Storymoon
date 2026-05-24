<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PayoutStatement extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'author_id',
        'month',
        'year',
        'total_gross_coins',
        'platform_fee_coins',
        'tax_coins',
        'net_author_coins',
        // [FIX] Tambah credited_at dari migrasi
        'credited_at',
        // [FIX] Hapus: 'status' — ini dokumen finansial resmi
        // Status hanya boleh diubah oleh PayoutService
    ];

    // [FIX] Tambah casts lengkap
    protected $casts = [
        'credited_at'         => 'datetime',
        'month'               => 'integer',
        'year'                => 'integer',
        'total_gross_coins'   => 'integer',
        'platform_fee_coins'  => 'integer',
        'tax_coins'           => 'integer',
        'net_author_coins'    => 'integer',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeCalculated(Builder $query): Builder
    {
        return $query->where('status', 'calculated');
    }

    public function scopeCredited(Builder $query): Builder
    {
        return $query->where('status', 'credited_to_wallet');
    }

    public function scopeForPeriod(Builder $query, int $month, int $year): Builder
    {
        return $query->where('month', $month)->where('year', $year);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Kreditkan koin ke wallet author
     * Hanya dipanggil dari PayoutService dengan DB transaction
     */
    public function markAsCredited(): void
    {
        $this->status      = 'credited_to_wallet';
        $this->credited_at = now();
        $this->save();
    }

    public function markAsFailed(): void
    {
        $this->status = 'failed';
        $this->save();
    }
}
