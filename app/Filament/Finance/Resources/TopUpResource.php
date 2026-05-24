<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Resources\TopUpResource\Pages;
use App\Models\TopUp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TopUpResource extends Resource
{
    protected static ?string $model = TopUp::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Riwayat Top-Up';
    protected static ?string $modelLabel = 'Top-Up Koin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Invoice Pembaca')
                    ->schema([
                        Forms\Components\Placeholder::make('buyer_name')
                            ->label('Nama Pembaca')
                            ->content(fn(TopUp $record): string => $record->user->name),
                        Forms\Components\TextInput::make('reference_id')
                            ->disabled()
                            ->label('ID Referensi (Midtrans/Sistem)'),
                        Forms\Components\TextInput::make('amount_rupiah')
                            ->disabled()
                            ->label('Nominal Bayar (Rupiah)')
                            ->prefix('Rp ')
                            ->numeric(),
                        Forms\Components\TextInput::make('coins_granted')
                            ->disabled()
                            ->label('Koin yang Didapat')
                            ->suffix(' Koin')
                            ->helperText('Dihitung otomatis: Rp 100 = 1 Koin'),
                        Forms\Components\TextInput::make('payment_method')
                            ->disabled()
                            ->label('Metode Pembayaran')
                            ->placeholder('Belum memilih metode pembayaran...'),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Verifikasi Finansial')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Menunggu Pembayaran (Pending)',
                                'success' => 'Pembayaran Sukses (Success)',
                                'failed' => 'Gagal (Failed)',
                                'expired' => 'Kedaluwarsa (Expired)',
                            ])
                            ->required()
                            ->label('Status Transaksi')
                            ->dehydrated(false), // 🌟 FIX!
                        Forms\Components\DateTimePicker::make('settled_at')
                            ->label('Waktu Pembayaran Diverifikasi')
                            ->disabled(), // Hanya terisi otomatis saat status sukses
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_id')->searchable()->label('ID Referensi'),
                Tables\Columns\TextColumn::make('user.name')->searchable()->label('Pembaca'),
                Tables\Columns\TextColumn::make('amount_rupiah')
                    ->money('IDR')
                    ->sortable()
                    ->label('Total Bayar'),
                Tables\Columns\TextColumn::make('coins_granted')
                    ->sortable()
                    ->label('Koin Didapat'),
                Tables\Columns\TextColumn::make('payment_method')->label('Metode'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'success',
                        'danger' => ['failed', 'expired'],
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Tanggal Buat'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Detail / Verifikasi'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopUps::route('/'),
            // Hapus rute 'create' karena invoice Top-Up hanya boleh dibuat dari Frontend oleh Pembaca
            'edit' => Pages\EditTopUp::route('/{record}/edit'),
        ];
    }
}
