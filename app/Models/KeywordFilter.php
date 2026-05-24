<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class KeywordFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'replacement',
        // [FIX] Tambah severity dari migrasi
        'severity',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeHighSeverity(Builder $query): Builder
    {
        return $query->where('severity', 'high');
    }
}
