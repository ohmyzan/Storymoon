<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Cache;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Global Config';
    protected static ?string $title = 'Pengaturan Global Aplikasi';

    protected static string $settings = GeneralSettings::class;

    // ✅ Clear cache setiap kali settings disimpan
    protected function afterSave(): void
    {
        Cache::put(
            'general_settings',
            app(GeneralSettings::class)->toArray(), // ✅ Hapus ->fresh()
            now()->addDay()
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Pengaturan')
                    ->tabs([

                        // TAB 1: FINANSIAL
                        Forms\Components\Tabs\Tab::make('Finansial')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('coin_price')
                                    ->label('Harga per Koin (Rupiah)')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('min_withdrawal')
                                    ->label('Minimum Penarikan (Rupiah)')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('revenue_share_exclusive')
                                    ->label('Bagi Hasil Eksklusif (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required(),

                                Forms\Components\TextInput::make('revenue_share_non_exclusive')
                                    ->label('Bagi Hasil Non-Eksklusif (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required(),
                            ])->columns(2),

                        // TAB 2: BONUS BULANAN
                        Forms\Components\Tabs\Tab::make('Bonus Bulanan')
                            ->icon('heroicon-o-gift')
                            ->schema([
                                Forms\Components\TextInput::make('bonus_min_chapters')
                                    ->label('Minimum Chapter untuk Bonus')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('bonus_min_words')
                                    ->label('Minimum Kata untuk Bonus')
                                    ->numeric()
                                    ->required(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Keamanan & Limitasi')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\TextInput::make('max_daily_chapters')
                                    ->label('Maksimal Chapter per Hari')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\Toggle::make('maintenance_mode')
                                    ->label('Mode Maintenance')
                                    ->helperText('Aktifkan untuk menutup akses publik sementara.')
                                    ->columnSpanFull(),

                                // ✅ Tambahkan ini
                                Forms\Components\Textarea::make('maintenance_message')
                                    ->label('Pesan Maintenance')
                                    ->placeholder('Contoh: Server akan down 30 menit untuk update database.')
                                    ->helperText('Pesan ini ditampilkan kepada user saat maintenance aktif.')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // TAB 4: UI & PENGUMUMAN
                        Forms\Components\Tabs\Tab::make('UI & Pengumuman')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Forms\Components\Textarea::make('announcement_text')
                                    ->label('Teks Pengumuman Berjalan (Marquee)')
                                    ->placeholder('Contoh: Promo diskon top-up koin 50%!')
                                    ->helperText('Kosongkan jika tidak ada pengumuman.')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),

                        // TAB 5: IDENTITAS PUBLIK & SEO
                        Forms\Components\Tabs\Tab::make('Identitas Publik & SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\TextInput::make('support_email')
                                    ->label('Email Bantuan (Support)')
                                    ->email()
                                    ->required(),

                                Forms\Components\TextInput::make('discord_url')
                                    ->label('Link Komunitas Discord')
                                    ->url()
                                    ->nullable(),

                                Forms\Components\TextInput::make('instagram_url')
                                    ->label('Link Instagram Resmi')
                                    ->url()
                                    ->nullable(),

                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Deskripsi SEO (Untuk Google)')
                                    ->maxLength(160)
                                    ->helperText('Maksimal 160 karakter agar optimal di mesin pencari.')
                                    ->columnSpanFull(),
                            ])->columns(2),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
