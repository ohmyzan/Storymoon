<?php

namespace App\Filament\Editor\Resources;

use App\Filament\Editor\Resources\NovelReportResource\Pages;
use App\Models\NovelReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NovelReportResource extends Resource
{
    protected static ?string $model = NovelReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'Laporan Novel';
    protected static ?string $navigationGroup = 'Manajemen Laporan';
    protected static ?string $modelLabel = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Laporan (Dari Pembaca)')
                    ->schema([
                        Forms\Components\Placeholder::make('reporter_name')
                            ->label('Pelapor')
                            ->content(fn(NovelReport $record): string => $record->reporter->name ?? 'User Tidak Diketahui'),
                        Forms\Components\Placeholder::make('novel_title')
                            ->label('Novel yang Dilaporkan')
                            ->content(fn(NovelReport $record): string => $record->novel->title ?? 'Novel Tidak Ditemukan'),

                        Forms\Components\TextInput::make('category')
                            ->disabled()
                            ->label('Kategori Pelanggaran'),
                        Forms\Components\Textarea::make('description')
                            ->disabled()
                            ->label('Deskripsi Laporan Pembaca')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('proof_image')
                            ->disabled()
                            ->label('Bukti Screenshot (Jika Ada)')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Tindakan Editor')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Belum Ditangani',
                                'reviewed' => 'Sedang Diinvestigasi',
                                'resolved' => 'Selesai (Pelanggaran Terbukti/Diperbaiki)',
                                'rejected' => 'Tolak Laporan (Laporan Palsu)',
                                'escalated' => '🔥 Eskalasi ke Admin (Pelanggaran Berat)',
                            ])
                            ->required()
                            ->label('Status Penanganan'),

                        Forms\Components\Textarea::make('editor_notes')
                            ->label('Catatan Penanganan Editor')
                            ->placeholder('Tuliskan tindakan apa yang sudah Anda lakukan pada penulis ini...')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('novel.title')->searchable()->label('Novel'),
                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'danger' => 'plagiarism',
                        'warning' => 'inappropriate_content',
                        'secondary' => 'spam',
                    ])
                    ->label('Kategori'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'pending',
                        'warning' => 'reviewed',
                        'success' => 'resolved',
                        'danger' => 'rejected',
                        'primary' => 'escalated',
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Waktu Laporan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Belum Ditangani',
                        'escalated' => 'Dieskalasi ke Admin',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Tindak Lanjuti'),
            ]);
    }

    /**
     * KEAMANAN MULTLAK: Editor HANYA bisa melihat laporan untuk novel yang DIA supervisi
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('novel', function (Builder $query) {
                $query->where('editor_id', Auth::id());
            })
            // 🌟 FIX DARI CLAUDE: Sembunyikan laporan yang sudah selesai agar tidak nyampah
            ->whereNotIn('status', ['resolved', 'rejected']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNovelReports::route('/'),
            // Kita hapus rute 'create' karena laporan hanya dibuat oleh Reader dari Frontend
            'edit' => Pages\EditNovelReport::route('/{record}/edit'),
        ];
    }
}
