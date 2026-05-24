<?php

namespace App\Filament\Admin\Resources\NovelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model; // Wajib ditambahkan
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
                        $data['word_count'] = str_word_count(strip_tags($data['content']));

                        // 🌟 FIX: Tangkap status default untuk create, tapi keluarkan dari array $data
                        $data['temp_status'] = 'published'; // Asumsi admin create langsung terbit

                        return $data;
                    })
                    // 🌟 FIX: Bypass $fillable untuk proses Create
                    ->using(function (array $data, string $model): Model {
                        $status = $data['temp_status'] ?? 'published';
                        unset($data['temp_status']);

                        $record = new $model($data);
                        $record->novel_id = $this->getOwnerRecord()->id; // Wajib kaitkan ke novel
                        $record->status = $status;

                        // Set tanggal terbit
                        if ($status === 'published') {
                            $record->published_at = now();
                        }

                        $record->save();
                        return $record;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['word_count'] = str_word_count(strip_tags($data['content']));

                        // Jika form Edit Admin memiliki dropdown status, kita amankan nilainya
                        if (isset($data['status'])) {
                            $data['temp_status'] = $data['status'];
                            unset($data['status']);
                        }

                        return $data;
                    })
                    // 🌟 FIX: Bypass $fillable untuk proses Edit Admin
                    ->using(function (Model $record, array $data): Model {
                        $newStatus = $data['temp_status'] ?? null;
                        unset($data['temp_status']);

                        $record->fill($data);

                        if ($newStatus) {
                            $record->status = $newStatus;

                            // Logika tanggal terbit
                            if ($newStatus === 'published' && $record->published_at === null) {
                                $record->published_at = now();
                            }
                        }

                        $record->save();
                        return $record;
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
