<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CommentReportResource\Pages;
use App\Models\CommentReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentReportResource extends Resource
{
    protected static ?string $model = CommentReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Eskalasi Komentar';
    protected static ?string $modelLabel = 'Eskalasi Laporan Komentar';
    protected static ?string $navigationGroup = 'Manajemen Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggaran & Catatan Moderator')
                    ->schema([
                        Forms\Components\Placeholder::make('comment_author')
                            ->label('Terdakwa (Penulis Komentar)')
                            ->content(fn(CommentReport $record): string => $record->comment->user->name ?? 'User Dihapus'),

                        // [FIX] Textarea tidak mendukung content(), diganti menjadi Placeholder
                        Forms\Components\Placeholder::make('comment_content')
                            ->label('Barang Bukti (Komentar Asli)')
                            ->content(fn(CommentReport $record): string => $record->comment->content ?? 'Komentar tidak ditemukan.')
                            ->columnSpanFull(),

                        // [FIX] Mengganti Textarea disabled menjadi Placeholder untuk teks statis
                        Forms\Components\Placeholder::make('moderator_notes')
                            ->label('Catatan/Alasan Eskalasi dari Moderator')
                            ->content(fn(CommentReport $record): string => $record->moderator_notes ?? '-')
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('Palu Hakim (Tindakan Admin)')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'escalated' => 'Sedang Diinvestigasi',
                                'resolved' => 'Selesai Dieksekusi',
                            ])
                            ->required()
                            ->label('Status Kasus'),

                        Forms\Components\Toggle::make('ban_user')
                            ->label('HUKUMAN MATI: Ban User Ini Secara Permanen')
                            ->helperText('Awas! Jika diaktifkan, akun pembuat komentar tidak akan bisa login selamanya.')
                            ->onColor('danger')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('comment.user.name')
                    ->label('Terdakwa')
                    ->searchable()
                    ->weight('bold')
                    ->color('danger'),
                Tables\Columns\BadgeColumn::make('reason')
                    ->colors(['danger' => ['spam', 'toxic', 'harassment']])
                    ->label('Kategori Pelanggaran'),
                Tables\Columns\TextColumn::make('moderator_notes')
                    ->label('Pesan Moderator')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Waktu Lapor'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sidang Tinggi'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'escalated');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommentReports::route('/'),
            'edit' => Pages\EditCommentReport::route('/{record}/edit'),
        ];
    }
}
