<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contract extends Model
{
    use HasFactory, HasUlids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'novel_id',
        'author_id',
        'editor_id',
        'contract_type',
        'revenue_share_author',
        'revenue_share_platform',
        // Data KYC — dienkripsi via $casts di bawah
        'real_name',
        'id_card_number',
        'id_card_image',
        'selfie_image',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'external_links',
        // [FIX] Rename signature_image → signature_image_path sesuai migrasi
        'signature_image_path',
        'contract_document_path',
        // [FIX] Hapus: 'status', 'editor_notes', 'signed_at'
        // Status hanya boleh diubah lewat ContractWorkflowService
    ];

    protected $casts = [
        'signed_at'              => 'datetime',
        'revenue_share_author'   => 'integer',
        'revenue_share_platform' => 'integer',
        // [FIX] WAJIB — enkripsi data PII & finansial sensitif (UU PDP)
        // Data di DB tersimpan terenkripsi, otomatis decrypt saat diakses via model
        'real_name'           => 'encrypted',
        'id_card_number'      => 'encrypted',
        'bank_account_number' => 'encrypted',
        'bank_account_name'   => 'encrypted',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                // [NOTE] Jangan log kolom KYC yang terenkripsi ke activity log
                'novel_id',
                'author_id',
                'editor_id',
                'contract_type',
                'status',
                'signed_at',
            ])
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Validasi total revenue share harus selalu = 100
     * Dipanggil di ContractService sebelum create/update
     */
    public function isRevenueShareValid(): bool
    {
        return ($this->revenue_share_author + $this->revenue_share_platform) === 100;
    }

    /**
     * Pindahkan ke status berikutnya dalam workflow.
     * Dipanggil dari ContractWorkflowService — bukan mass assignment.
     */
    public function advanceTo(string $status, ?string $notes = null): void
    {
        $this->status       = $status;
        $this->editor_notes = $notes;

        if ($status === 'active') {
            $this->signed_at = now();
        }

        $this->save();
    }
}
