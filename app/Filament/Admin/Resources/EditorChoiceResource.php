<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EditorChoiceResource\Pages;
use App\Models\EditorChoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
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
        // Admin hanya membaca alasan dari editor, tidak membuat dari nol
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pengajuan')
                    ->schema([
                        Forms\Components\Placeholder::make('novel_title')
                            ->label('Novel')
                            ->content(fn(EditorChoice $record): string => $record->novel->title),

                        Forms\Components\Placeholder::make('editor_name')
                            ->label('Diajukan Oleh Editor')
                            ->content(fn(EditorChoice $record): string => $record->editor->name),

                        Forms\Components\Textarea::make('editor_notes')
                            ->disabled()
                            ->label('Analisis & Alasan Editor'),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan Admin (Opsional)')
                            ->placeholder('Masukkan alasan jika Anda menolak pengajuan ini...'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->with(['novel', 'editor'])) // 🌟 Tambahkan with() di sini
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
                // TOMBOL APPROVE (SETUJUI TAYANG)
                Action::make('approve_nomination')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(EditorChoice $record) => $record->status === 'pending')
                    ->action(fn(EditorChoice $record) => $record->update(['status' => 'approved'])),

                // TOMBOL REJECT (TOLAK TAYANG)
                Action::make('reject_nomination')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(EditorChoice $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->required()
                            ->label('Alasan Penolakan'),
                    ])
                    ->action(fn(EditorChoice $record, array $data) => $record->update([
                        'status' => 'rejected',
                        'admin_notes' => $data['admin_notes']
                    ])),

                Tables\Actions\EditAction::make()->label('Detail'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEditorChoices::route('/'),
            'edit' => Pages\EditEditorChoice::route('/{record}/edit'),
        ];
    }
}
