<?php

namespace App\Filament\Admin\Resources\NovelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';
    protected static ?string $title = 'Daftar Bab (Chapters)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Bab'),

                Forms\Components\TextInput::make('chapter_number')
                    ->numeric()
                    ->required()
                    ->label('Bab Ke-'),

                Forms\Components\Toggle::make('is_premium')
                    ->live()
                    ->label('Bab Premium (Koin)'),

                Forms\Components\TextInput::make('coin_price')
                    ->numeric()
                    ->visible(fn(Forms\Get $get) => $get('is_premium'))
                    ->required(fn(Forms\Get $get) => $get('is_premium'))
                    ->default(0)
                    ->label('Harga Koin'),

                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->label('Isi Cerita'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            // Penting agar fitur SoftDeletes (Tong Sampah) bekerja di dalam tabel relasi
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->columns([
                Tables\Columns\TextColumn::make('chapter_number')
                    ->sortable()
                    ->label('Bab Ke-'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Judul Bab'),

                Tables\Columns\IconColumn::make('is_premium')
                    ->boolean()
                    ->trueIcon('heroicon-o-currency-dollar')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->label('Premium'),

                Tables\Columns\TextColumn::make('word_count')
                    ->label('Jumlah Kata')
                    ->toggleable(), // Bisa disembunyikan/ditampilkan
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), // Filter untuk melihat bab yang dihapus
                Tables\Filters\TernaryFilter::make('is_premium')
                    ->label('Status Premium'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Fitur Otomatisasi: Hitung jumlah kata dari isi cerita (buang tag HTML-nya)
                        $data['word_count'] = str_word_count(strip_tags($data['content']));
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Fitur Otomatisasi: Update hitungan jumlah kata saat diedit
                        $data['word_count'] = str_word_count(strip_tags($data['content']));
                        return $data;
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
}
