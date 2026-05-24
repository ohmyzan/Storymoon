<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'novel_id',
        'rating',
        'content',
    ];

    // [FIX] Tambah casts — tinyInteger perlu di-cast agar tidak jadi string
    protected $casts = [
        'rating' => 'integer',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    // [FIX] Tambah relasi ke laporan review
    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }
}
