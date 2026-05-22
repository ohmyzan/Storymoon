<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Resources\ChapterPurchaseResource\Pages;
use App\Models\ChapterPurchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChapterPurchaseResource extends Resource
{
    protected static ?string $model = ChapterPurchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $navigationLabel = 'Jurnal Pendapatan Bab';

    protected static ?string $modelLabel = 'Log Transaksi Bab';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Audit Detail Transaksi')
                    ->schema([
                        Forms\Components\Placeholder::make('reader')
                            ->label('Pembaca (Reader)')
                            ->content(fn(?ChapterPurchase $record): string => $record?->reader?->name ?? 'N/A'),

                        Forms\Components\Placeholder::make('author')
                            ->label('Penulis (Author)')
                            ->content(fn(?ChapterPurchase $record): string => $record?->author?->name ?? 'N/A'),

                        Forms\Components\Placeholder::make('chapter')
                            ->label('Bab Novel')
                            ->content(fn(?ChapterPurchase $record): string => $record?->chapter?->title ?? 'N/A'),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Waktu Transaksi')
                            ->content(
                                fn(?ChapterPurchase $record): string =>
                                $record?->created_at?->format('d M Y H:i:s') ?? '-'
                            ),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Rincian Pembagian Koin (Ledger)')
                    ->schema([
                        Forms\Components\TextInput::make('price_coined')
                            ->disabled()
                            ->label('Total Koin Dibayar'),

                        Forms\Components\TextInput::make('author_earning')
                            ->disabled()
                            ->label('Porsi Penulis (Author Earning)'),

                        Forms\Components\TextInput::make('platform_earning')
                            ->disabled()
                            ->label('Porsi Platform (Platform Earning)'),

                        Forms\Components\TextInput::make('contract_type_snapshot')
                            ->disabled()
                            ->label('Tipe Kontrak Saat Transaksi'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->label('Waktu'),

                Tables\Columns\TextColumn::make('reader.name')
                    ->searchable()
                    ->label('Pembaca'),

                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->label('Penulis'),

                Tables\Columns\TextColumn::make('chapter.title')
                    ->searchable()
                    ->label('Bab'),

                Tables\Columns\TextColumn::make('price_coined')
                    ->alignEnd()
                    ->label('Koin'),

                Tables\Columns\TextColumn::make('author_earning')
                    ->alignEnd()
                    ->color('success')
                    ->label('Porsi Author'),

                Tables\Columns\TextColumn::make('platform_earning')
                    ->alignEnd()
                    ->color('primary')
                    ->label('Porsi Platform'),

                Tables\Columns\BadgeColumn::make('contract_type_snapshot')
                    ->colors([
                        'primary' => 'exclusive',
                        'success' => 'non_exclusive',
                    ])
                    ->label('Snapshot Kontrak'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('contract_type_snapshot')
                    ->options([
                        'exclusive' => 'Eksklusif (70:30)',
                        'non_exclusive' => 'Non-Eksklusif (50:50)',
                    ])
                    ->label('Filter Tipe Kontrak'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Audit'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChapterPurchases::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
