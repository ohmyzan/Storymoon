<?php

namespace App\Support;

use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;

class SettingsCache
{
  const KEY = 'general_settings';
  const TTL = 60 * 60 * 24; // 24 jam

  public static function get(): array
  {
    return Cache::remember(
      self::KEY,
      self::TTL,
      fn() => app(GeneralSettings::class)->toArray()
    );
  }

  public static function flush(): void
  {
    // ✅ Atomic: langsung tulis data baru, tidak ada gap kosong
    Cache::put(
      self::KEY,
      app(GeneralSettings::class)->toArray(),
      self::TTL
    );
  }
}
