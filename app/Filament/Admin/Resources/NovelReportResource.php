<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NovelReportResource\Pages;
use App\Models\NovelReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NovelReportResource extends Resource
{
    protected static ?string $model = NovelReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Eskalasi Novel';
    protected static ?string $modelLabel = 'Eskalasi Laporan Novel';
    protected static ?string $navigationGroup = 'Manajemen Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Investigasi Editor')
                    ->schema([
                        Forms\Components\Placeholder::make('novel_title')
                            ->label('Novel Bermasalah')
                            ->content(fn(NovelReport $record): string => $record->novel->title ?? 'Novel Dihapus'),
                        Forms\Components\Placeholder::make('author_name')
                            ->label('Penulis Asli')
                            ->content(fn(NovelReport $record): string => $record->novel->author->name ?? 'User Dihapus'),
                        Forms\Components\Placeholder::make('reporter_name')
                            ->label('Dieskalasi Oleh (Editor)')
                            ->content(fn(NovelReport $record): string => $record->reporter->name ?? 'Sistem'),

                        // [FIX] reason adalah Enum/String pendek, gunakan TextInput bukan Textarea
                        Forms\Components\TextInput::make('reason')
                            ->label('Kategori Pelanggaran Berat')
                            ->disabled(),

                        // [FIX] Gunakan placeholder untuk teks yang tidak bisa diedit
                        Forms\Components\Placeholder::make('description')
                            ->label('Barang Bukti / Catatan Editor')
                            ->content(fn(NovelReport $record): string => $record->description ?? '-')
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Keputusan Eksekusi Admin')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'escalated' => 'Sedang Diinvestigasi',
                                'resolved' => 'Selesai Dieksekusi',
                            ])
                            ->required()
                            ->label('Status Kasus'),

                        Forms\Components\Toggle::make('takedown_novel')
                            ->label('TAKEDOWN: Turunkan Novel Ini dari Platform')
                            ->helperText('Jika diaktifkan, novel ini akan diubah statusnya menjadi Draft/Unpublished dan tidak bisa dibaca publik.')
                            ->onColor('danger')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('novel.author'))
            ->columns([
                Tables\Columns\TextColumn::make('novel.title')
                    ->label('Novel Bermasalah')
                    ->searchable()
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('novel.author.name')
                    ->label('Penulis'),
                Tables\Columns\BadgeColumn::make('reason')
                    ->colors(['danger' => ['plagiarism', 'illegal_content', 'copyright']])
                    ->label('Pelanggaran'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Tanggal Eskalasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sidang Hak Cipta'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'escalated');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNovelReports::route('/'),
            'edit' => Pages\EditNovelReport::route('/{record}/edit'),
        ];
    }
}
