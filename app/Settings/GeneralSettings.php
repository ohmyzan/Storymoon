<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
  public int $coin_price;
  public int $min_withdrawal;
  public int $revenue_share_exclusive;
  public int $revenue_share_non_exclusive;

  public int $bonus_min_chapters;
  public int $bonus_min_words;

  // Tab 3: Keamanan & Limitasi
  public int $max_daily_chapters;
  public bool $maintenance_mode;
  public ?string $maintenance_message; // ✅ Tambahkan ini

  public ?string $announcement_text;

  public ?string $support_email;
  public ?string $discord_url;
  public ?string $instagram_url;
  public ?string $meta_description;

  public static function group(): string
  {
    return 'general';
  }
}
