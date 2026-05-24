<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NovelView extends Model
{
  use HasUlids;

  // [FIX] Matikan timestamps bawaan Laravel
  // Migrasi tidak punya created_at/updated_at — hanya ada viewed_at
  // Ini juga meningkatkan performa insert pada tabel yang sangat high-volume
  public $timestamps = false;

  protected $fillable = [
    'novel_id',
    'user_id',
    'ip_address',
    'session_id',
    'viewed_at',
  ];

  protected $casts = [
    'viewed_at' => 'datetime',
  ];

  // Tidak perlu SoftDeletes — log view tidak perlu di-restore
  // Tidak perlu LogsActivity — terlalu noisy untuk tabel high-volume

  // =========================================================
  // RELASI
  // =========================================================

  public function novel(): BelongsTo
  {
    return $this->belongsTo(Novel::class);
  }

  /**
   * Nullable — guest view tidak punya user_id
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
