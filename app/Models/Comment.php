<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'novel_id',
        'chapter_id',
        'paragraph_id',
        'content',
        'is_hidden'
    ];
}
