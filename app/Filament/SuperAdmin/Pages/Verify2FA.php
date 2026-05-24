<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Services\Google2FAService; // 🌟 Panggil Service Kita
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Verify2FA extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $slug = 'verify-2fa';
    protected static string $view = 'filament.super-admin.pages.verify2fa';
    protected static bool $shouldRegisterNavigation = false;

    public ?string $otp = '';

    public function mount(): void
    {
        $session = app('session');

        if ($session->get('2fa_verified')) {
            redirect()->to('/super-admin');
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('otp')
                    ->label('Masukkan Kode Authenticator')
                    ->required()
                    ->length(6)
                    ->autofocus()
                    ->extraAttributes([
                        'class' => 'text-center text-3xl font-bold tracking-widest',
                    ]),
            ]);
    }

    public function verify()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            Notification::make()->title('Session login tidak ditemukan.')->danger()->send();
            return redirect('/login');
        }

        $isValid = app(Google2FAService::class)->verifyOtp($user->google2fa_secret, $this->otp);

        if ($isValid) {
            app('session')->put('2fa_verified', true);

            Notification::make()
                ->title('Akses Diberikan!')
                ->success()
                ->send();

            return redirect()->to('/super-admin');
        }

        Notification::make()
            ->title('Kode Salah atau Kadaluarsa!')
            ->danger()
            ->send();
    }
}
