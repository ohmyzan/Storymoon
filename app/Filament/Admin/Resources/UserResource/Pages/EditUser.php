<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    // 🌟 LOGIKA EDIT DIPISAH DI SINI
    protected function getHeaderActions(): array
    {
        return [
            // Verifikasi Author
            Actions\Action::make('verify')
                ->label(fn(User $record) => $record->author_verified_at ? 'Cabut Verifikasi Author' : 'Verifikasi Author')
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->visible(fn(User $record) => $record->hasRole('Author'))
                ->action(function (User $record) {
                    $record->update(['author_verified_at' => $record->author_verified_at ? null : now()]);
                    $this->refreshFormData(['author_verified_at']);
                }),

            // Verifikasi Editor
            Actions\Action::make('verify_editor')
                ->label(fn(User $record) => $record->editor_verified_at ? 'Cabut Verifikasi Editor' : 'Verifikasi Editor')
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->visible(fn(User $record) => $record->hasRole('Editor'))
                ->action(function (User $record) {
                    $record->update(['editor_verified_at' => $record->editor_verified_at ? null : now()]);
                    $this->refreshFormData(['editor_verified_at']);
                }),

            // Eksekusi Banned
            Actions\Action::make('ban_user')
                ->label(fn(User $record) => $record->banned_at ? 'Cabut Blokir (Unban)' : 'Blokir User (Ban)')
                ->icon(fn(User $record) => $record->banned_at ? 'heroicon-o-arrow-path' : 'heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (User $record) {
                    if ($record->banned_at) {
                        $record->unban(); // Menggunakan fungsi dari Model!
                    } else {
                        $record->ban();
                    }
                    $this->refreshFormData(['banned_at']);
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
