<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EditorChoiceResource\Pages;
use App\Models\EditorChoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EditorChoiceResource extends Resource
{
    protected static ?string $model = EditorChoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';
    protected static ?string $navigationGroup = 'Manajemen Konten';
    protected static ?string $modelLabel = 'Pengajuan Pilihan Editor';

    public static function form(Form $form): Form
    {
        // 🌟 FIX: Dikosongkan karena form sudah dimatikan di EditEditorChoice.php 
        // Halaman edit murni menjadi halaman "Review Kasus"
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->with(['novel', 'editor']))
            ->columns([
                Tables\Columns\TextColumn::make('novel.title')
                    ->searchable()
                    ->sortable()
                    ->label('Novel'),

                Tables\Columns\TextColumn::make('editor.name')
                    ->label('Editor Pengaju'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->label('Status'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->label('Tanggal Diajukan'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu Review',
                        'approved' => 'Disetujui (Tayang)',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                // 🌟 FIX: Tombol Setujui dan Tolak dihapus dari tabel! 
                // Logika eksekusinya sudah aman tersimpan di Header Actions halaman Edit.
                Tables\Actions\EditAction::make()->label('Review & Eksekusi'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEditorChoices::route('/'),
            // EditAction akan mengarah ke sini, di mana tombol Setujui/Tolak berada
            'edit' => Pages\EditEditorChoice::route('/{record}/edit'),
        ];
    }
}
