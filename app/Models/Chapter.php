<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Chapter extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'novel_id',
        'title',
        'slug',
        'content',
        'word_count',
        'chapter_number',
        'editor_notes',
        'published_at',
        'is_premium',
        'coin_price',
        // [NOTE] 'status' tidak di $fillable — diubah lewat workflow
    ];

    // [FIX] Tambah casts lengkap
    protected $casts = [
        'is_premium'     => 'boolean',
        'published_at'   => 'datetime',
        'coin_price'     => 'integer',
        'word_count'     => 'integer',
        'chapter_number' => 'integer',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    // [FIX] Tambah relasi ke pembelian chapter
    public function purchases(): HasMany
    {
        return $this->hasMany(ChapterPurchase::class);
    }

    // [FIX] Tambah relasi ke komentar chapter
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Chapter yang sudah dipublish dan waktunya sudah tiba
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Chapter yang masih terjadwal (belum waktunya tayang)
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query
            ->where('status', 'scheduled')
            ->where('published_at', '>', now());
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Cek apakah chapter ini sudah dibeli oleh user tertentu.
     * Sangat sering dibutuhkan untuk gate akses chapter premium.
     */
    public function isPurchasedBy(User $user): bool
    {
        return $this->purchases()
            ->where('reader_id', $user->id)
            ->exists();
    }

    /**
     * Apakah chapter ini bisa diakses gratis oleh user?
     * Gratis jika: bukan premium, atau sudah dibeli user tersebut.
     */
    public function isAccessibleBy(?User $user): bool
    {
        if (! $this->is_premium) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        return $this->isPurchasedBy($user);
    }
}
