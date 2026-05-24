<?php

namespace App\Filament\Admin\Resources\NovelResource\Pages;

use App\Filament\Admin\Resources\NovelResource;
use App\Models\Novel;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNovel extends EditRecord
{
    protected static string $resource = NovelResource::class;

    // 🌟 LOGIKA EDIT DIPISAH DI SINI (Header Actions)
    protected function getHeaderActions(): array
    {
        return [
            // Tombol Eksekusi Freeze dipindah ke halaman Edit (Lebih aman dari salah klik di tabel)
            Actions\Action::make('freeze')
                ->label(fn(Novel $record) => $record->isFrozen() ? 'Unfreeze Novel' : 'Freeze Novel')
                ->icon(fn(Novel $record) => $record->isFrozen() ? 'heroicon-o-check-circle' : 'heroicon-o-no-symbol')
                ->color(fn(Novel $record) => $record->isFrozen() ? 'success' : 'danger')
                ->requiresConfirmation()
                ->modalHeading(fn(Novel $record) => $record->isFrozen() ? 'Buka Pembekuan Novel?' : 'Bekukan Novel Ini?')
                ->modalDescription('Novel yang dibekukan tidak akan bisa dibaca oleh Reader.')
                ->action(function (Novel $record) {
                    if ($record->isFrozen()) {
                        $record->transitionTo('published', $record->publish_status);
                    } else {
                        $record->transitionTo('frozen');
                    }

                    // Refresh data di UI setelah mutasi
                    $this->refreshFormData(['status']);
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
