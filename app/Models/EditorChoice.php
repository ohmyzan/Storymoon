<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EditorChoice extends Model
{
    use HasFactory, HasUlids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'novel_id',
        'editor_id',
        'editor_notes',
        // [FIX] Hapus: 'status', 'admin_notes'
        // Status hanya boleh diubah lewat AdminApprovalService
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
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
     * Approve nominasi — hanya dipanggil dari AdminApprovalService
     */
    public function approve(string $adminNotes): void
    {
        $this->status      = 'approved';
        $this->admin_notes = $adminNotes;
        $this->save();
    }

    public function reject(string $adminNotes): void
    {
        $this->status      = 'rejected';
        $this->admin_notes = $adminNotes;
        $this->save();
    }
}
