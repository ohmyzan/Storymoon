<?php

namespace App\Filament\SuperAdmin\Resources\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\UserResource;
use App\Models\User;
use App\Services\Google2FAService; // 🌟 Panggil Service Kita
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // VERIFY AUTHOR
            Actions\Action::make('verify_author')
                ->label(fn(User $record) => $record->author_verified_at ? 'Unverify Author' : 'Verify Author')
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->visible(fn(User $record) => $record->hasRole('Author'))
                ->action(function (User $record) {
                    $record->update([
                        'author_verified_at' => $record->author_verified_at ? null : now(),
                    ]);
                    $this->refreshFormData(['author_verified_at']);
                }),

            // SETUP 2FA (Sangat Clean!)
            Actions\Action::make('setup_2fa')
                ->label(fn(User $record) => $record->google2fa_secret ? 'Reset 2FA' : 'Aktifkan 2FA')
                ->icon('heroicon-o-qr-code')
                ->color(fn(User $record) => $record->google2fa_secret ? 'warning' : 'success')
                ->visible(fn(User $record) => $record->id === Auth::id())
                ->form(function (User $record) {
                    // 🌟 Menggunakan Service
                    $google2faService = app(Google2FAService::class);
                    $secret = $google2faService->generateSecret();
                    $svg = $google2faService->generateQrCodeSvg($record, $secret);

                    return [
                        Forms\Components\Hidden::make('secret')->default($secret),
                        Forms\Components\Placeholder::make('qr')
                            ->label('1. Scan QR dengan Google Authenticator')
                            ->content(
                                new HtmlString('<div class="flex justify-center p-4 bg-white rounded-lg">' . $svg . '</div>')
                            ),
                        Forms\Components\TextInput::make('otp')
                            ->label('2. Masukkan 6 Digit OTP')
                            ->required()
                            ->length(6)
                            ->numeric(),
                    ];
                })
                ->action(function (User $record, array $data) {
                    // 🌟 Menggunakan Service
                    $isValid = app(Google2FAService::class)->verifyOtp($data['secret'], $data['otp']);

                    if ($isValid) {
                        $record->update(['google2fa_secret' => $data['secret']]);
                        app('session')->put('2fa_verified', true);
                        Notification::make()->title('2FA berhasil diaktifkan!')->success()->send();
                    } else {
                        Notification::make()->title('Kode OTP salah!')->danger()->send();
                    }
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
