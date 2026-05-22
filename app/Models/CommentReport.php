<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class CommentReport extends Model
{
    use HasUlids;

    protected $fillable = [
        'reporter_id',
        'comment_id',
        'reason',
        'description',
        'status',
        'moderator_notes',
        'handled_by',
    ];

    /**
     * Relasi ke Pembaca yang melaporkan
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Relasi ke Komentar yang dilaporkan
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Relasi ke Staf Moderator/Admin yang menangani kasus ini
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
