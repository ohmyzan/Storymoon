<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'target_url',
        'is_active',
        'sort_order',
        // [FIX] Tambah kolom baru dari migrasi
        'start_date',
        'end_date',
        'created_by',
    ];

    // [FIX] Tambah casts agar tipe data konsisten
    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    // [FIX] Tambah relasi ke user yang membuat banner
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Banner yang benar-benar tayang saat ini:
     * is_active = true DAN dalam rentang tanggal (jika ada)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(
                fn(Builder $q) => $q
                    ->whereNull('start_date')
                    ->orWhere('start_date', '<=', now())
            )
            ->where(
                fn(Builder $q) => $q
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', now())
            );
    }

    /**
     * Urutan tampil sesuai sort_order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
