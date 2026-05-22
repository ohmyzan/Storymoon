<?php

namespace App\Filament\Moderator\Resources;

use App\Filament\Moderator\Resources\ReviewReportResource\Pages;
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
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Laporan Ulasan';
    protected static ?string $modelLabel = 'Laporan Ulasan';
    protected static ?string $navigationGroup = 'Laporan Komunitas'; // Mengelompokkan menu agar rapi

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Ulasan yang Dilaporkan')
                    ->schema([
                        Forms\Components\Placeholder::make('reviewer_name')
                            ->label('Penulis Ulasan')
                            ->content(fn(ReviewReport $record): string => $record->review->user->name ?? 'User Dihapus'),
                        Forms\Components\Placeholder::make('novel_title')
                            ->label('Novel yang Diulas')
                            ->content(fn(ReviewReport $record): string => $record->review->novel->title ?? 'Novel Dihapus'),
                        Forms\Components\TextInput::make('rating_snapshot')
                            ->label('Bintang yang Diberikan')
                            ->disabled()
                            // Asumsi kita menampilkan rating dari relasi
                            ->formatStateUsing(fn(ReviewReport $record): string => ($record->review->rating ?? 0) . ' Bintang ⭐️'),
                        Forms\Components\Textarea::make('review_content')
                            ->label('Isi Ulasan')
                            ->content(fn(ReviewReport $record): string => $record->review->content ?? 'Ulasan tidak ditemukan.')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Pelapor')
                    ->schema([
                        Forms\Components\Placeholder::make('reporter_name')
                            ->label('Dilaporkan Oleh')
                            ->content(fn(ReviewReport $record): string => $record->reporter->name ?? 'User Dihapus'),
                        Forms\Components\TextInput::make('reason')
                            ->label('Kategori Pelanggaran')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label('Catatan dari Pelapor')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Tindakan Moderator')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Menunggu Keputusan',
                                'resolved' => 'Hapus Ulasan (Terbukti Melanggar)',
                                'rejected' => 'Tolak Laporan (Ulasan Aman)',
                                'escalated' => '🔥 Eskalasi ke Admin',
                            ])
                            ->required()
                            ->label('Keputusan Eksekusi'),

                        Forms\Components\Textarea::make('moderator_notes')
                            ->label('Catatan Moderator')
                            ->placeholder('Alasan penghapusan atau penolakan...')
                            ->required()
                            ->columnSpanFull(),
                    ])->color('warning'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Tarik data ulasan dan judul novel sekaligus!
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->with('review.novel'))
            ->columns([
                Tables\Columns\TextColumn::make('novel.title')
                    ->label('Novel')
                    ->searchable()
                    ->getStateUsing(fn(ReviewReport $record): string => $record->review->novel->title ?? '-'),
                Tables\Columns\BadgeColumn::make('reason')
                    ->colors([
                        'danger' => ['review_bombing', 'hate_speech', 'harassment'],
                        'warning' => 'spoiler',
                        'secondary' => 'other',
                    ])
                    ->label('Alasan'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'resolved',
                        'danger' => 'rejected',
                        'primary' => 'escalated',
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Waktu Lapor'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'resolved' => 'Resolved',
                        'escalated' => 'Escalated',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sidang / Proses'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviewReports::route('/'),
            // Kita matikan tombol Create manual karena laporan murni dari frontend
            'edit' => Pages\EditReviewReport::route('/{record}/edit'),
        ];
    }
}
