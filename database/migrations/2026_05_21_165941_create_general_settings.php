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

        // ✅ FIX: Tambah pesan maintenance dinamis
        $this->migrator->add(
            'general.maintenance_message',
            'Kami sedang melakukan peningkatan sistem agar pengalaman membacamu semakin menyenangkan. Mohon kembali beberapa saat lagi.'
        );

        $this->migrator->add('general.announcement_text', null);

        // ✅ FIX: Field baru untuk Tab 5 (Identitas Publik & SEO)
        $this->migrator->add('general.support_email', 'support@storymoon.com');
        $this->migrator->add('general.discord_url', null);
        $this->migrator->add('general.instagram_url', null);
        $this->migrator->add('general.meta_description', 'Platform Web Novel Modern Terbaik.');
    }
};
