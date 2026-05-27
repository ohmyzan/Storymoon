<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Novel extends Model
{
    use HasFactory, HasUlids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'author_id',
        'editor_id',
        'title',
        'slug',
        'synopsis',
        'cover_image',
        // [FIX] Tambah publish_status dari migrasi baru
        'publish_status',
        // [FIX] Hapus: 'status' — diubah lewat NovelWorkflowService
        // [FIX] Hapus: 'is_frozen' — kolom ini sudah dihapus dari migrasi
        // [FIX] Hapus: 'views_count', 'favorites_count', 'total_chapters', 'rating'
        //       Counter cache hanya boleh diubah via increment/decrement di Observer
    ];

    protected $casts = [
        'rating'          => 'decimal:2',
        'views_count'     => 'integer',
        'favorites_count' => 'integer',
        'total_chapters'  => 'integer',
        'published_at'    => 'datetime',
        // [FIX] Hapus: 'is_frozen' — kolom sudah tidak ada
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'publish_status', 'editor_id', 'published_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // =========================================================
    // RELASI — USER
    // =========================================================

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    // =========================================================
    // RELASI — KONTEN
    // =========================================================

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('chapter_number');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'novel_genre')
            ->withPivot('created_at');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function novelViews(): HasMany
    {
        return $this->hasMany(NovelView::class);
    }

    // [FIX] Tambah relasi reports yang hilang
    public function reports(): HasMany
    {
        return $this->hasMany(NovelReport::class);
    }

    // =========================================================
    // RELASI — TRANSAKSI
    // =========================================================

    /**
     * [FIX] Ganti hasManyThrough → hasMany langsung
     * chapter_purchases sudah punya novel_id langsung di migrasi yang diperbaiki
     */
    public function chapterPurchases(): HasMany
    {
        return $this->hasMany(ChapterPurchase::class);
    }

    // =========================================================
    // RELASI — KONTRAK
    // =========================================================

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract(): HasOne
    {
        return $this->hasOne(Contract::class)
            ->where('status', 'active')
            ->latestOfMany('signed_at');
    }

    // =========================================================
    // RELASI — EDITOR CHOICE & EVENT
    // =========================================================

    public function editorChoices(): HasMany
    {
        return $this->hasMany(EditorChoice::class);
    }

    public function eventParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('publish_status', 'ongoing');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('publish_status', 'completed');
    }

    /**
     * Novel yang sedang dibekukan oleh moderator
     */
    public function scopeFrozen(Builder $query): Builder
    {
        return $query->where('status', 'frozen');
    }

    /**
     * Novel dengan status Editor Choice yang sudah approved
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->whereHas(
            'editorChoices',
            fn(Builder $q) => $q->where('status', 'approved')
        );
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * [FIX] Ganti kolom is_frozen yang sudah dihapus
     * dengan helper yang membaca dari kolom status
     */
    public function isFrozen(): bool
    {
        return $this->status === 'frozen';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Ubah status novel — hanya dipanggil dari NovelWorkflowService
     */
    public function transitionTo(string $status, ?string $publishStatus = null): void
    {
        $this->status = $status;

        if ($publishStatus !== null) {
            $this->publish_status = $publishStatus;
        }

        if ($status === 'published' && $this->published_at === null) {
            $this->published_at = now();
        }

        $this->save();
    }
}
