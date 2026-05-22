<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
  // Tab 1: Finansial
  public int $coin_price;
  public int $min_withdrawal;
  public int $revenue_share_exclusive;
  public int $revenue_share_non_exclusive;

  // Tab 2: Bonus Bulanan
  public int $bonus_min_chapters;
  public int $bonus_min_words;

  // Tab 3: Keamanan & Limitasi
  public int $max_daily_chapters;
  public bool $maintenance_mode;

  // Tab 4: UI & Pengumuman
  public ?string $announcement_text;

  public static function group(): string
  {
    return 'general';
  }
}
