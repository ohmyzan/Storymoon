<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ReviewReport extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'reporter_id',
        'review_id',
        'reason',
        'description',
        'status',
        'moderator_notes',
        'handled_by',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
    public function review()
    {
        return $this->belongsTo(Review::class);
    }
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
