<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Resources\PayoutStatementResource\Pages;
use App\Models\PayoutStatement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PayoutStatementResource extends Resource
{
    protected static ?string $model = PayoutStatement::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Payout Bulanan';
    protected static ?string $modelLabel = 'Slip Payout';
    protected static ?string $navigationGroup = 'Rekapitulasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Periode & Penulis')
                    ->schema([
                        Forms\Components\Placeholder::make('author_name')
                            ->label('Nama Penulis')
                            ->content(fn(PayoutStatement $record): string => $record->author->name ?? 'N/A'),
                        Forms\Components\TextInput::make('month')
                            ->disabled()
                            ->label('Bulan'),
                        Forms\Components\TextInput::make('year')
                            ->disabled()
                            ->label('Tahun'),
                    ])->columns(3),

                Forms\Components\Section::make('Rincian Finansial (Dalam Koin)')
                    ->schema([
                        Forms\Components\TextInput::make('total_gross_coins')
                            ->disabled()
                            ->label('Pendapatan Kotor')
                            ->numeric(),
                        Forms\Components\TextInput::make('platform_fee_coins')
                            ->disabled()
                            ->label('Potongan Platform (Fee)')
                            ->numeric(),
                        Forms\Components\TextInput::make('tax_coins')
                            ->disabled()
                            ->label('Pajak (Tax)')
                            ->numeric(),
                        Forms\Components\TextInput::make('net_author_coins')
                            ->disabled()
                            ->label('Pendapatan Bersih (Netto)')
                            ->helperText('Jumlah koin yang berhak masuk ke dompet penulis.')
                            ->numeric(),
                    ])->columns(2),

                Forms\Components\Section::make('Status Distribusi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'calculated' => 'Selesai Dihitung (Belum Masuk Dompet)',
                                'credited_to_wallet' => 'Telah Disuntikkan ke Dompet Penulis',
                            ])
                            ->required()
                            ->label('Status Slip Pendapatan')
                            ->dehydrated(false), // 🌟 FIX: Jangan masukkan ke array penyimpanan default
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->label('Penulis'),
                Tables\Columns\TextColumn::make('month')
                    ->sortable()
                    ->label('Bulan'),
                Tables\Columns\TextColumn::make('year')
                    ->sortable()
                    ->label('Tahun'),
                Tables\Columns\TextColumn::make('net_author_coins')
                    ->alignEnd()
                    ->sortable()
                    ->label('Koin Bersih'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'calculated',
                        'success' => 'credited_to_wallet',
                    ])
                    ->label('Status'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'calculated' => 'Belum Masuk Dompet',
                        'credited_to_wallet' => 'Sudah Masuk Dompet',
                    ]),
                Tables\Filters\SelectFilter::make('month')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ])
                    ->label('Filter Bulan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Lihat / Update Status'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayoutStatements::route('/'),
            'edit' => Pages\EditPayoutStatement::route('/{record}/edit'),
        ];
    }
}
