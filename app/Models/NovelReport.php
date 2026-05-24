<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class NovelReport extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'reporter_id',
        'novel_id',
        'category',
        'description',
        'proof_image',
        // [FIX] Hapus: 'status', 'editor_notes', 'handled_by'
        // Kolom moderasi hanya boleh diubah eksplisit di ModerationService
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
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
    public function resolve(User $handler, string $notes): void
    {
        $this->status       = 'resolved';
        $this->editor_notes = $notes;
        $this->handled_by   = $handler->id;
        $this->save();
    }

    public function reject(User $handler, string $notes): void
    {
        $this->status       = 'rejected';
        $this->editor_notes = $notes;
        $this->handled_by   = $handler->id;
        $this->save();
    }

    public function escalate(User $handler, string $notes): void
    {
        $this->status       = 'escalated';
        $this->editor_notes = $notes;
        $this->handled_by   = $handler->id;
        $this->save();
    }
}
