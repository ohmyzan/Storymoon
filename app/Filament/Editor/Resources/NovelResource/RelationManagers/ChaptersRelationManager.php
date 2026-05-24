<?php

namespace App\Filament\Editor\Resources\NovelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    protected static ?string $title = 'Daftar Bab & Review';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Naskah Penulis (Read-Only)')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->disabled()
                            ->label('Judul Bab'),

                        Forms\Components\TextInput::make('chapter_number')
                            ->disabled()
                            ->label('Bab Ke-'),

                        Forms\Components\Toggle::make('is_premium')
                            ->disabled()
                            ->label('Status: Bab Berbayar (Premium)'),

                        Forms\Components\RichEditor::make('content')
                            ->disabled()
                            ->columnSpanFull()
                            ->label('Isi Cerita'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Ruang Revisi Editor')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'review' => 'Sedang Direview',
                                'revision_needed' => 'Kembalikan (Perlu Revisi)',
                                'scheduled' => 'Lolos (Antre Terjadwal)',
                                'published' => 'Lolos (Terbit Langsung)',
                            ])
                            ->required()
                            ->label('Status Kelayakan Bab'),

                        Forms\Components\Textarea::make('editor_notes')
                            ->label('Catatan Revisi untuk Penulis')
                            ->placeholder('Contoh: Tolong perbaiki typo di paragraf ke-3...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(
                fn(Builder $query) => $query->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('chapter_number')
                    ->label('Bab Ke-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Bab'),

                // 🌟 FIX CLAUDE: Tambahkan kolom Word Count!
                Tables\Columns\TextColumn::make('word_count')
                    ->label('Jumlah Kata')
                    ->suffix(' Kata')
                    ->sortable(),

                // INDIKATOR PREMIUM UNTUK EDITOR
                Tables\Columns\IconColumn::make('is_premium')
                    ->boolean()
                    ->trueIcon('heroicon-o-currency-dollar')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->label('Premium'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'review',
                        'danger' => 'revision_needed',
                        'info' => 'scheduled',
                        'success' => 'published',
                    ])
                    ->label('Status Kelayakan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Review & Nilai')
                    ->icon('heroicon-o-magnifying-glass-circle')

                    // Simpan status lama
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['old_status'] = $data['status'] ?? 'draft';

                        return $data;
                    })

                    // Custom update logic
                    ->using(function (Model $record, array $data): Model {
                        $oldStatus = $data['old_status'] ?? 'draft';

                        unset($data['old_status']);

                        // Jika pertama kali dipublish
                        if (
                            $oldStatus !== 'published' &&
                            ($data['status'] ?? null) === 'published'
                        ) {
                            $data['published_at'] = now();
                        }

                        $record->update($data);

                        return $record;
                    }),
            ]);
    }
}
