<?php

namespace App\Filament\Admin\Resources; // Perhatikan namespacenya sekarang sangat clean!
use App\Filament\Admin\Resources\NovelResource\Pages;
use App\Models\Novel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope; // Wajib dipanggil untuk SoftDeletes
use Filament\Tables\Actions\Action;

class NovelResource extends Resource
{
    protected static ?string $model = Novel::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Manajemen Konten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('author_id')
                    ->relationship('author', 'name', fn(Builder $query) => $query->role('Author')) // Secure: Hanya tampilkan user dengan role Author!
                    ->required()
                    ->searchable() // Scalable: Jika author ada ribuan, sistem tidak akan crash
                    ->label('Penulis'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Novel'),
                Forms\Components\Textarea::make('synopsis')
                    ->required()
                    ->columnSpanFull()
                    ->label('Sinopsis'),
                Forms\Components\Select::make('status')
                    ->options([
                        'bersambung' => 'Bersambung',
                        'tamat' => 'Tamat',
                    ])
                    ->required()
                    ->default('bersambung'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Judul Novel'),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->label('Penulis'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'bersambung',
                        'success' => 'tamat',
                    ]),
                Tables\Columns\TextColumn::make('total_chapters')
                    ->sortable()
                    ->label('Total Bab'),

                Tables\Columns\IconColumn::make('is_frozen')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->label('Dibekukan'),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), // Fitur Keamanan: Admin bisa melihat dan merestore data yang dihapus!
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'bersambung' => 'Bersambung',
                        'tamat' => 'Tamat',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Cukup sisakan satu di paling atas

                // TOMBOL FREEZE NOVEL
                Action::make('freeze')
                    ->label(fn(Novel $record) => $record->is_frozen ? 'Unfreeze' : 'Freeze')
                    ->icon(fn(Novel $record) => $record->is_frozen ? 'heroicon-o-check-circle' : 'heroicon-o-no-symbol')
                    ->color(fn(Novel $record) => $record->is_frozen ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn(Novel $record) => $record->is_frozen ? 'Buka Pembekuan Novel?' : 'Bekukan Novel Ini?')
                    ->modalDescription('Novel yang dibekukan tidak akan bisa dibaca oleh Reader.')
                    ->action(function (Novel $record) {
                        $record->update(['is_frozen' => !$record->is_frozen]);
                    }),

                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan "NovelResource\" agar PHP tahu jalurnya dengan benar
            NovelResource\RelationManagers\ChaptersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNovels::route('/'),
            'create' => Pages\CreateNovel::route('/create'),
            'edit' => Pages\EditNovel::route('/{record}/edit'),
        ];
    }

    // WAJIB DITAMBAHKAN AGAR SOFTDELETES BERFUNGSI DI FILAMENT
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
