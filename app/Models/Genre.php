<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Genre extends Model
{
    // [NOTE] Genre menggunakan BIGINT id (bukan ULID) — sudah benar sesuai migrasi
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * Sub-genre (anak) dari genre ini
     */
    public function children(): HasMany
    {
        return $this->hasMany(Genre::class, 'parent_id');
    }

    /**
     * Genre induk dari sub-genre ini
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'parent_id');
    }

    /**
     * Novel yang memiliki genre ini
     * [FIX] Tambah withTimestamps() untuk memanfaatkan created_at di pivot
     */
    public function novels(): BelongsToMany
    {
        return $this->belongsToMany(Novel::class, 'novel_genre')
            ->withTimestamps(['created_at']); // <--- INI PENYEBABNYA 🚨
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Hanya genre level atas (bukan sub-genre)
     */
    public function scopeParent(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Hanya sub-genre (punya induk)
     */
    public function scopeChildren(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }
}
