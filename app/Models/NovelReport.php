<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

class  NovelReport extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'reporter_id',
        'novel_id',
        'category',
        'description',
        'proof_image',
        'status',
        'editor_notes',
        'handled_by',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
