<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CommentReport extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'reporter_id',
        'comment_id',
        'reason',
        'description',
        // [FIX] Hapus: 'status', 'moderator_notes', 'handled_by'
        // Kolom ini hanya boleh diubah eksplisit oleh moderator di service layer
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
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
     * Tandai laporan sudah ditangani moderator.
     * Dipanggil eksplisit di ModerationService, bukan lewat mass assignment.
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
