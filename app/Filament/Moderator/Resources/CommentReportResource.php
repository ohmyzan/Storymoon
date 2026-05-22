<?php

namespace App\Filament\Moderator\Resources;

use App\Filament\Moderator\Resources\CommentReportResource\Pages;
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
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'Laporan Komentar';
    protected static ?string $modelLabel = 'Laporan Komentar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Komentar yang Dilaporkan')
                    ->schema([
                        Forms\Components\Placeholder::make('comment_author')
                            ->label('Penulis Komentar')
                            ->content(fn(CommentReport $record): string => $record->comment->user->name ?? 'User Dihapus'),
                        Forms\Components\Textarea::make('comment_content')
                            ->label('Isi Komentar Asli')
                            ->content(fn(CommentReport $record): string => $record->comment->content ?? 'Komentar tidak ditemukan.')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('comment_hidden_status')
                            ->label('Status Disembunyikan (Saat Ini)')
                            ->disabled()
                            ->default(fn(CommentReport $record): bool => $record->comment->is_hidden ?? false),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Pelapor')
                    ->schema([
                        Forms\Components\Placeholder::make('reporter_name')
                            ->label('Dilaporkan Oleh')
                            ->content(fn(CommentReport $record): string => $record->reporter->name ?? 'User Dihapus'),
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
                                'resolved' => 'Selesai (Komentar Disembunyikan)',
                                'rejected' => 'Tolak Laporan (Komentar Aman)',
                                'escalated' => '🔥 Eskalasi ke Admin (Perlu Banned Akun)',
                            ])
                            ->required()
                            ->label('Keputusan (Status)'),

                        Forms\Components\Textarea::make('moderator_notes')
                            ->label('Catatan Moderator')
                            ->placeholder('Tulis alasan tindakan Anda di sini...')
                            ->required()
                            ->columnSpanFull(),
                    ])->color('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Tarik data pelapor dan komentar sekaligus!
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->with(['reporter', 'comment']))
            ->columns([
                Tables\Columns\TextColumn::make('reporter.name')->label('Pelapor')->searchable(),
                Tables\Columns\BadgeColumn::make('reason')
                    ->colors([
                        'danger' => ['spam', 'toxic', 'harassment'],
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
                    ->label('Status Penanganan'),
                Tables\Columns\IconColumn::make('comment.is_hidden')
                    ->boolean()
                    ->label('Komentar Tersembunyi'),
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
            'index' => Pages\ListCommentReports::route('/'),
            // Hapus route Create karena Laporan murni dibuat oleh Pembaca dari Frontend
            'edit' => Pages\EditCommentReport::route('/{record}/edit'),
        ];
    }
}
