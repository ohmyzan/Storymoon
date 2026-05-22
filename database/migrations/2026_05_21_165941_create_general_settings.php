<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Nilai default awal saat sistem pertama kali menyala
        $this->migrator->add('general.coin_price', 100);
        $this->migrator->add('general.min_withdrawal', 350000);
        $this->migrator->add('general.revenue_share_exclusive', 70);
        $this->migrator->add('general.revenue_share_non_exclusive', 50);

        $this->migrator->add('general.bonus_min_chapters', 25);
        $this->migrator->add('general.bonus_min_words', 30000);

        $this->migrator->add('general.max_daily_chapters', 5);
        $this->migrator->add('general.maintenance_mode', false);

        $this->migrator->add('general.announcement_text', null);
    }
};
