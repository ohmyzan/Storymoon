<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NovelResource\Pages;
use App\Models\Novel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->relationship('author', 'name', fn(Builder $query) => $query->role('Author'))
                    ->required()
                    ->searchable()
                    ->label('Penulis'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Novel'),
                Forms\Components\Textarea::make('synopsis')
                    ->required()
                    ->columnSpanFull()
                    ->label('Sinopsis'),

                Forms\Components\Placeholder::make('status_display')
                    ->label('Status Sistem')
                    ->content(fn(?Novel $record) => $record ? strtoupper($record->status) : 'DRAFT'),

                Forms\Components\Select::make('publish_status')
                    ->options([
                        'ongoing' => 'Bersambung',
                        'completed' => 'Tamat',
                    ])
                    ->nullable()
                    ->label('Status Publikasi Cerita')
                    ->visible(fn(Forms\Get $get, ?Novel $record) => $record && $record->status === 'published'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->label('Judul Novel'),
                Tables\Columns\TextColumn::make('author.name')->searchable()->sortable()->label('Penulis'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['secondary' => 'draft', 'success' => 'published', 'danger' => 'frozen']),
                Tables\Columns\TextColumn::make('total_chapters')->sortable()->label('Total Bab'),
                Tables\Columns\IconColumn::make('is_frozen')
                    ->getStateUsing(fn(Novel $record) => $record->isFrozen())
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->label('Dibekukan'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'frozen' => 'Dibekukan']),
                Tables\Filters\SelectFilter::make('publish_status')
                    ->options(['ongoing' => 'Bersambung', 'completed' => 'Tamat']),
            ])
            ->actions([
                // 🌟 FIX: Tombol Freeze dihapus dari sini. Logikanya ada di EditNovel.php
                Tables\Actions\EditAction::make()->label('Detail & Eksekusi'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
