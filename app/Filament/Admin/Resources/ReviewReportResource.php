<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewReportResource\Pages;
use App\Models\ReviewReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReviewReportResource extends Resource
{
    protected static ?string $model = ReviewReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Eskalasi Ulasan';
    protected static ?string $modelLabel = 'Eskalasi Laporan Ulasan';
    protected static ?string $navigationGroup = 'Manajemen Laporan'; // Mengelompokkan menu agar rapi

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Barang Bukti Ulasan & Catatan Moderator')
                    ->schema([
                        Forms\Components\Placeholder::make('reviewer')
                            ->label('Terdakwa (Penulis Ulasan)')
                            ->content(fn(ReviewReport $record): string => $record->review->user->name ?? 'User Dihapus'),
                        Forms\Components\Placeholder::make('novel')
                            ->label('Novel Terkait')
                            ->content(fn(ReviewReport $record): string => $record->review->novel->title ?? 'Novel Dihapus'),
                        Forms\Components\Placeholder::make('rating')
                            ->label('Rating Bintang')
                            ->content(fn(ReviewReport $record): string => ($record->review->rating ?? 0) . ' Bintang ⭐️'),
                        Forms\Components\Textarea::make('review_content')
                            ->label('Isi Ulasan Kontroversial')
                            ->content(fn(ReviewReport $record): string => $record->review->content ?? 'Content tidak ditemukan.')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('moderator_notes')
                            ->label('Alasan Lemparan Kasus dari Moderator')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Keputusan Tertinggi Admin')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'escalated' => 'Sedang Diinvestigasi',
                                'resolved' => 'Selesai (Ulasan Dihapus & Kasus Ditutup)',
                            ])
                            ->required()
                            ->label('Status Laporan'),

                        Forms\Components\Toggle::make('ban_user')
                            ->label('HUKUMAN MATI: Blokir Akun Pembuat Ulasan Secara Permanen')
                            ->helperText('Gunakan ini jika terbukti akun klonengan/bot yang melakukan review bombing.')
                            ->onColor('danger')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('review.user.name')
                    ->label('Penulis Ulasan')
                    ->searchable()
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('review.novel->title')
                    ->label('Novel')
                    ->getStateUsing(fn(ReviewReport $record): string => $record->review->novel->title ?? '-'),
                Tables\Columns\BadgeColumn::make('reason')
                    ->colors(['danger' => 'review_bombing'])
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Tanggal Eskalasi'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('Eksekusi Kasus'),
            ]);
    }

    // 🔒 PENTING: Filter otomatis agar Admin hanya melihat laporan berstatus 'escalated'
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'escalated');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviewReports::route('/'),
            'edit' => Pages\EditReviewReport::route('/{record}/edit'),
        ];
    }
}
