<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GenreResource\Pages;
use App\Models\Genre;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class GenreResource extends Resource
{
    protected static ?string $model = Genre::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Manajemen Konten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Genre Utama (Kosongkan jika ini adalah Genre Utama)')
                    ->relationship('parent', 'name')
                    ->searchable(),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->label('Nama Genre'),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->unique(Genre::class, 'slug', ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Genre'),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Sub-Genre Dari')
                    ->sortable()
                    ->badge(), // Bikin tampilannya cantik sebagai badge
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug'),
            ])
            ->filters([
                // Filter untuk melihat genre utama saja atau sub genre saja
                Tables\Filters\Filter::make('genre_utama')
                    ->query(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereNull('parent_id'))
                    ->label('Hanya Genre Utama'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGenres::route('/'),
            'create' => Pages\CreateGenre::route('/create'),
            'edit' => Pages\EditGenre::route('/{record}/edit'),
        ];
    }
    // Catatan: Genre tidak pakai soft deletes berdasarkan instruksi awal, jadi tidak ada TrashedFilter disini.
}
