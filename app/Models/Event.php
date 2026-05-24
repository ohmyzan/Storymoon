<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_image',
        'start_date',
        'end_date',
        // [FIX] Tambah created_by dari migrasi
        'created_by',
        // [NOTE] 'status' tidak di $fillable — diubah lewat EventService
    ];

    // [FIX] Tambah casts untuk tanggal
    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    // [FIX] Tambah relasi creator
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Event yang sedang berjalan saat ini
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Event yang akan datang
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query
            ->where('status', 'active')
            ->where('start_date', '>', now());
    }

    // =========================================================
    // HELPERS
    // =========================================================

    public function isOngoing(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function hasEnded(): bool
    {
        return $this->end_date->isPast();
    }
}
