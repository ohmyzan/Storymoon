<?php

namespace App\Filament\SuperAdmin\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class Verify2FA extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $slug = 'verify-2fa';

    // Blade view
    protected static string $view = 'filament.super-admin.pages.verify2fa';

    // Sembunyikan dari sidebar
    protected static bool $shouldRegisterNavigation = false;

    public ?string $otp = '';

    public function mount(): void
    {
        // Jika sudah lolos 2FA
        if (session()->get('2fa_verified')) {
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

        // Safety check
        if (! $user) {

            Notification::make()
                ->title('Session login tidak ditemukan.')
                ->danger()
                ->send();

            return redirect('/login');
        }

        $google2fa = app(Google2FA::class);

        // Verifikasi OTP
        $valid = $google2fa->verifyKey(
            $user->google2fa_secret,
            $this->otp
        );

        if ($valid) {

            session()->put('2fa_verified', true);

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
