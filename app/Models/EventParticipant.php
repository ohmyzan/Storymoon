<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class EventParticipant extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'event_id',
        'novel_id',
        // [FIX] Hapus: 'status' — diubah lewat EventReviewService
        // [FIX] Tambah reviewer_notes dari migrasi (catatan reviewer boleh di fillable)
        'reviewer_notes',
        // [NOTE] 'reviewed_by' tidak di $fillable — diisi eksplisit saat review
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    // [FIX] Tambah relasi reviewer
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Approve peserta — hanya dipanggil dari EventReviewService
     */
    public function approve(User $reviewer, ?string $notes = null): void
    {
        $this->status      = 'approved';
        $this->reviewed_by = $reviewer->id;
        $this->reviewer_notes = $notes;
        $this->save();
    }

    public function reject(User $reviewer, ?string $notes = null): void
    {
        $this->status      = 'rejected';
        $this->reviewed_by = $reviewer->id;
        $this->reviewer_notes = $notes;
        $this->save();
    }
}
