<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ReviewReport extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'reporter_id',
        'review_id',
        'reason',
        'description',
        // [FIX] Hapus: 'status', 'moderator_notes', 'handled_by'
        // Kolom moderasi hanya boleh diubah eksplisit di ModerationService
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeEscalated(Builder $query): Builder
    {
        return $query->where('status', 'escalated');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Selesaikan laporan — hanya dipanggil dari ModerationService
     */
    public function resolve(User $moderator, string $notes): void
    {
        $this->status          = 'resolved';
        $this->moderator_notes = $notes;
        $this->handled_by      = $moderator->id;
        $this->save();
    }

    public function reject(User $moderator, string $notes): void
    {
        $this->status          = 'rejected';
        $this->moderator_notes = $notes;
        $this->handled_by      = $moderator->id;
        $this->save();
    }
}
