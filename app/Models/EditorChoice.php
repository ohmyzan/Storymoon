<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

class EditorChoice extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'novel_id',
        'editor_id',
        'editor_notes',
        'status',
        'admin_notes',
    ];

    /**
     * Relasi ke Novel yang dinominasikan
     */
    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    /**
     * Relasi ke User (Editor) yang mengajukan
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
}
