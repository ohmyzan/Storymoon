<?php

namespace App\Filament\Admin\Resources\EditorChoiceResource\Pages;

use App\Filament\Admin\Resources\EditorChoiceResource;
use App\Models\EditorChoice;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditEditorChoice extends EditRecord
{
    protected static string $resource = EditorChoiceResource::class;

    // Hapus form bawaan agar halaman ini murni menjadi panel "Review Kasus"
    protected function getFormSchema(): array
    {
        return [];
    }

    // 🌟 LOGIKA KEPUTUSAN DIPISAH DI SINI
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve_nomination')
                ->label('Setujui Pengajuan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn(EditorChoice $record) => $record->status === 'pending')
                ->action(function (EditorChoice $record) {
                    $record->approve('Disetujui oleh Admin');
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\Action::make('reject_nomination')
                ->label('Tolak Pengajuan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(EditorChoice $record) => $record->status === 'pending')
                ->form([
                    Forms\Components\Textarea::make('admin_notes')
                        ->required()
                        ->label('Alasan Penolakan'),
                ])
                ->action(function (EditorChoice $record, array $data) {
                    $record->reject($data['admin_notes']);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
