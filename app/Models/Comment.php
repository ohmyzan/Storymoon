<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'novel_id',
        'chapter_id',
        // [FIX] Tambah parent_id untuk fitur nested reply
        'parent_id',
        'paragraph_id',
        'content',
        // [NOTE] 'is_hidden' tidak di $fillable — diubah oleh moderator eksplisit
        // [NOTE] 'likes_count' tidak di $fillable — diubah via increment/decrement
    ];

    // [FIX] Tambah casts
    protected $casts = [
        'is_hidden'   => 'boolean',
        'likes_count' => 'integer',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    // [FIX] Tambah semua relasi yang hilang
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Komentar induk (untuk reply)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Semua reply dari komentar ini
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CommentReport::class);
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Hanya komentar yang tidak disembunyikan moderator
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false);
    }

    /**
     * Hanya komentar level atas (bukan reply)
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Sembunyikan komentar (oleh moderator)
     * Dipanggil eksplisit, bukan lewat mass assignment
     */
    public function hide(): void
    {
        $this->is_hidden = true;
        $this->save();
    }

    public function unhide(): void
    {
        $this->is_hidden = false;
        $this->save();
    }
}
