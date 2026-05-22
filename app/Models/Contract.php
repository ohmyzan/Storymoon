<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'novel_id',
        'author_id',
        'editor_id',
        'contract_type',
        'revenue_share_author',
        'revenue_share_platform',
        'real_name',
        'id_card_number',
        'id_card_image',
        'selfie_image',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'external_links',
        'signature_image',
        'contract_document_path',
        'status',
        'editor_notes',
        'signed_at',
    ];

    /**
     * Casts variabel agar tipe datanya otomatis sesuai saat dipanggil
     */
    protected $casts = [
        'signed_at' => 'datetime',
        'revenue_share_author' => 'integer',
        'revenue_share_platform' => 'integer',
    ];

    /**
     * Relasi ke Novel yang dikontrak
     */
    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    /**
     * Relasi ke Author (Pemilik Novel)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Relasi ke Editor (Supervisor)
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
}
