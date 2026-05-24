<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
  // Model ini sudah baik — tidak perlu SoftDeletes (unfavorite = delete langsung)
  // Tidak perlu LogsActivity — terlalu noisy untuk interaksi user
  use HasFactory, HasUlids;

  protected $fillable = [
    'user_id',
    'novel_id',
  ];

  // =========================================================
  // RELASI
  // =========================================================

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function novel(): BelongsTo
  {
    return $this->belongsTo(Novel::class);
  }
}
