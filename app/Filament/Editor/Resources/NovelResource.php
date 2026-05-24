<?php

namespace App\Filament\Editor\Resources;

use App\Filament\Editor\Resources\NovelResource\Pages;
use App\Models\Novel;
use App\Models\EditorChoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NovelResource extends Resource
{
    protected static ?string $model = Novel::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Novel Supervisi';
    protected static ?string $modelLabel = 'Novel Binaan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Koreksi Metadata Novel')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Novel'),
                        Forms\Components\Textarea::make('synopsis')
                            ->required()
                            ->label('Sinopsis'),
                        Forms\Components\FileUpload::make('cover_image')
                            ->image()
                            ->directory('novel-covers')
                            ->label('Cover Novel'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->label('Cover'),
                Tables\Columns\TextColumn::make('title')->searchable()->label('Judul'),
                Tables\Columns\TextColumn::make('total_chapters')->label('Total Bab'),
                Tables\Columns\TextColumn::make('views_count')->label('Total Views')->sortable(),
                Tables\Columns\TextColumn::make('rating')->label('Rating'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Bina Metadata'),

                Action::make('nominate_editors_choice')
                    ->label('Ajukan Pilihan Editor')
                    ->icon('heroicon-o-hand-thumb-up')
                    ->color('star')
                    ->requiresConfirmation()
                    // 🌟 FIX DARI CLAUDE: Cegah SPAM! Cek apakah sudah pernah diajukan
                    ->visible(
                        fn(Novel $record) =>
                        $record->total_chapters >= 30 &&
                            !$record->editorChoices()->whereIn('status', ['pending', 'approved'])->exists()
                    )
                    ->form([
                        Forms\Components\Textarea::make('editor_notes')
                            ->required()
                            ->label('Analisis Performa & Alasan Rekomendasi')
                            ->placeholder('Tulis bukti data retensi...'),
                    ])
                    ->action(function (Novel $record, array $data) {
                        // 🌟 FIX: Hapus 'status' => 'pending' dari array mass-assignment
                        EditorChoice::create([
                            'novel_id' => $record->id,
                            'editor_id' => Auth::id(),
                            'editor_notes' => $data['editor_notes'],
                        ]);
                    }),
            ]);
    }

    /**
     * KEAMANAN MUTLAK: Editor hanya bisa melihat novel yang ditugaskan kepada dirinya sendiri!
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('editor_id', Auth::id()); // Memfilter berdasarkan ID Editor yang sedang login
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNovels::route('/'),
            'edit' => Pages\EditNovel::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            NovelResource\RelationManagers\ChaptersRelationManager::class,
        ];
    }
}
