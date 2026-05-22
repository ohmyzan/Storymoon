<?php

namespace App\Filament\Admin\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use App\Models\EventParticipant;
use Illuminate\Database\Eloquent\Builder;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';
    protected static ?string $title = 'Novel Peserta Lomba';

    public function form(Form $form): Form
    {
        // Form dikosongkan/dimatikan karena pendaftaran murni dari Pusat Penulis
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('novel.title')
            ->modifyQueryUsing(fn(Builder $query) => $query->with('novel.author')) // 🌟 Eager Load Relasi Bertingkat
            ->columns([
                Tables\Columns\TextColumn::make('novel.title')
                    ->label('Judul Novel')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('novel.author.name')
                    ->label('Nama Penulis')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status Peserta')
                    ->colors([
                        'success' => 'approved', // Aktif di lomba
                        'danger'  => 'rejected', // Didiskualifikasi
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'approved' => 'Peserta Aktif',
                        'rejected' => 'Didiskualifikasi',
                    ])
                    ->label('Filter Status'),
            ])
            ->headerActions([])
            ->actions([
                // TOMBOL DISKUALIFIKASI (Admin sebagai Polisi)
                Action::make('disqualify')
                    ->label('Diskualifikasi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Diskualifikasi Novel Ini?')
                    ->modalDescription('Novel ini akan dikeluarkan dari lomba (misal: karena melanggar tema).')
                    ->visible(fn(EventParticipant $record) => $record->status === 'approved')
                    ->action(fn(EventParticipant $record) => $record->update(['status' => 'rejected'])),

                // TOMBOL PULIHKAN (Jika Admin salah klik diskualifikasi)
                Action::make('restore_participant')
                    ->label('Pulihkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn(EventParticipant $record) => $record->status === 'rejected')
                    ->action(fn(EventParticipant $record) => $record->update(['status' => 'approved'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
