<?php

namespace App\Filament\Editor\Resources;

use App\Filament\Editor\Resources\TextReviewResource\Pages;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Tambahkan ini!
use Filament\Notifications\Notification; // Tambahkan ini!  

class TextReviewResource extends Resource
{
    protected static ?string $model = Contract::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $navigationLabel = 'Review Naskah Kontrak';
    protected static ?string $modelLabel = 'Review Kelayakan Naskah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengajuan')
                    ->description('Silakan baca 10 Bab pertama di menu "Novel Binaan" sebelum mengambil keputusan di sini.')
                    ->schema([
                        Forms\Components\Placeholder::make('novel_title')
                            ->label('Judul Novel')
                            ->content(fn(Contract $record): string => $record->novel->title),
                        Forms\Components\Placeholder::make('author_name')
                            ->label('Nama Penulis')
                            ->content(fn(Contract $record): string => $record->author->name),
                        Forms\Components\TextInput::make('contract_type')
                            ->disabled()
                            ->label('Tipe Kontrak yang Diajukan')
                            ->formatStateUsing(fn(string $state): string => $state === 'exclusive' ? 'Eksklusif (70:30)' : 'Non-Eksklusif (50:50)'),
                    ])->columns(3),

                // 🌟 TAMBAHAN BARU: Tombol rahasia untuk membaca naskah tanpa menjadi Supervisor dulu
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('baca_naskah')
                        ->label('📖 Baca 10 Bab Pertama')
                        ->color('info')
                        ->modalHeading(fn(Contract $record) => 'Pratinjau Naskah: ' . $record->novel->title)
                        ->modalWidth('7xl')
                        // 🌟 FIX RAM OOM: Gunakan chapters()->take(10)->get() BUKAN chapters->take(10)
                        ->modalContent(fn(Contract $record) => new \Illuminate\Support\HtmlString(
                            '<div class="p-6 bg-white dark:bg-gray-900 rounded-lg max-h-[70vh] overflow-y-auto prose dark:prose-invert max-w-none">' .
                                $record->novel->chapters()->take(10)->get()->map(function ($chapter) {
                                    return "<h2 class='text-2xl font-bold mb-4 mt-8'>{$chapter->chapter_number}. {$chapter->title}</h2>" .
                                        "<div class='text-gray-700 dark:text-gray-300 leading-relaxed'>" . $chapter->content . "</div>" .
                                        "<hr class='my-8 border-gray-300 dark:border-gray-700'>";
                                })->implode('') .
                                '</div>'
                        ))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup Naskah'),
                ])->columnSpanFull(),

                Forms\Components\Section::make('Keputusan Redaksi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'text_review' => 'Sedang Dibaca / Dipelajari',
                                'approved' => 'Setujui Naskah (Lolos Kurasi)',
                                'revision_needed' => 'Kembalikan (Perlu Revisi Teks)',
                                'rejected' => 'Tolak Kontrak (Kualitas Belum Memenuhi)',
                            ])
                            ->required()
                            ->label('Keputusan Editor'),

                        Forms\Components\Textarea::make('editor_notes')
                            ->label('Catatan Redaksi (Wajib diisi jika ditolak/revisi)')
                            ->placeholder('Berikan *feedback* yang membangun untuk penulis...')
                            ->columnSpanFull(),
                    ])->color('warning'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('novel.title')->searchable()->label('Novel'),
                Tables\Columns\TextColumn::make('author.name')->searchable()->label('Penulis'),
                Tables\Columns\BadgeColumn::make('contract_type')
                    ->colors([
                        'primary' => 'exclusive',
                        'success' => 'non_exclusive',
                    ])
                    ->formatStateUsing(fn(string $state): string => $state === 'exclusive' ? 'Eksklusif' : 'Non-Eksklusif')
                    ->label('Tipe'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->label('Tanggal Pengajuan'),
            ])
            ->actions([
                // 🌟 FIX RACE CONDITION: Menggunakan Transaction Lock!
                Tables\Actions\Action::make('claim_task')
                    ->label('Klaim Naskah')
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Ambil Alih Evaluasi Naskah?')
                    ->modalDescription('Setelah diklaim, Editor lain tidak akan bisa melihat atau mengevaluasi naskah ini.')
                    ->visible(fn(Contract $record) => is_null($record->editor_id))
                    ->action(function (Contract $record) {
                        try {
                            DB::transaction(function () use ($record) {
                                // Kunci row di DB agar tidak diembat editor lain di detik yang sama
                                $fresh = Contract::lockForUpdate()->find($record->id);

                                if ($fresh->editor_id !== null) {
                                    throw new \Exception('Naskah sudah diklaim editor lain.');
                                }

                                $fresh->update(['editor_id' => Auth::id()]);
                            });
                            Notification::make()->title('Sukses diklaim!')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Gagal Klaim')->body($e->getMessage())->danger()->send();
                        }
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Evaluasi Naskah')
                    ->visible(fn(Contract $record) => $record->editor_id === Auth::id()),
            ]);
    }

    // 🌟 PENGUNCIAN MUTLAK: Editor HANYA melihat kontrak yang berstatus text_review
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'text_review')
            ->where(function (Builder $query) {
                $query->whereNull('editor_id') // Yang masih menganggur di kolam
                    ->orWhere('editor_id', Auth::id()); // ATAU yang sudah saya klaim
            })
            ->with(['novel', 'author']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTextReviews::route('/'),
            // Create dimatikan. Data dikirim dari Penulis (Frontend)
            'edit' => Pages\EditTextReview::route('/{record}/edit'),
        ];
    }
}
