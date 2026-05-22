<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    // Mengatur Icon, Grup Menu, dan Judul di Sidebar
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Global Config';
    protected static ?string $title = 'Pengaturan Global Aplikasi';

    // Menghubungkan halaman ini dengan class settings kita
    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Pengaturan')
                    ->tabs([
                        // TAB 1: FINANSIAL
                        Forms\Components\Tabs\Tab::make('Sistem Finansial')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\TextInput::make('coin_price')
                                    ->label('Harga 1 Koin (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                Forms\Components\TextInput::make('min_withdrawal')
                                    ->label('Batas Minimal Withdrawal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                Forms\Components\TextInput::make('revenue_share_exclusive')
                                    ->label('Bagi Hasil Kontrak Eksklusif')
                                    ->numeric()
                                    ->suffix('% Author')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required(),

                                Forms\Components\TextInput::make('revenue_share_non_exclusive')
                                    ->label('Bagi Hasil Kontrak Non-Eksklusif')
                                    ->numeric()
                                    ->suffix('% Author')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required(),
                            ])->columns(2),

                        // TAB 2: BONUS BULANAN
                        Forms\Components\Tabs\Tab::make('Bonus Bulanan')
                            ->icon('heroicon-o-gift')
                            ->schema([
                                Forms\Components\TextInput::make('bonus_min_chapters')
                                    ->label('Target Minimal Bab / Bulan')
                                    ->numeric()
                                    ->suffix('Bab')
                                    ->required(),

                                Forms\Components\TextInput::make('bonus_min_words')
                                    ->label('Target Minimal Kata / Bulan')
                                    ->numeric()
                                    ->suffix('Kata')
                                    ->required(),
                            ])->columns(2),

                        // TAB 3: KEAMANAN & LIMITASI
                        Forms\Components\Tabs\Tab::make('Keamanan & Limitasi')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\TextInput::make('max_daily_chapters')
                                    ->label('Batas Maksimal Upload Bab Per Hari')
                                    ->numeric()
                                    ->helperText('Mencegah Author melakukan spamming bab.')
                                    ->required(),

                                Forms\Components\Toggle::make('maintenance_mode')
                                    ->label('MODE DARURAT: Aktifkan Maintenance')
                                    ->helperText('HATI-HATI! Jika aktif, seluruh website pembaca akan ditutup sementara. Panel Admin tetap terbuka.')
                                    ->onColor('danger')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // TAB 4: UI & PENGUMUMAN
                        Forms\Components\Tabs\Tab::make('UI & Pengumuman')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Forms\Components\Textarea::make('announcement_text')
                                    ->label('Teks Pengumuman Berjalan (Marquee)')
                                    ->placeholder('Contoh: Promo diskon top-up koin 50% hanya untuk akhir pekan ini!')
                                    ->helperText('Kosongkan jika tidak ada pengumuman.')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(), // Agar tab melebar memenuhi layar
            ]);
    }
}
