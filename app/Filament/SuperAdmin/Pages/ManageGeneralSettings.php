<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Global Config';
    protected static ?string $title = 'Pengaturan Global Aplikasi';

    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Pengaturan')
                    ->tabs([
                        // ... [TAB 1 (Finansial), TAB 2 (Bonus), TAB 3 (Keamanan) biarkan SAMA PERSIS seperti milik Anda] ...

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

                        // 🌟 FIX: TAB 5 WAJIB DITAMBAHKAN UNTUK KEBUTUHAN FRONTEND REACT KITA
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
