<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pencairan Dana';
    protected static ?string $modelLabel = 'Pencairan Dana';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Penarikan & Rekening Tujuan')
                    ->schema([
                        Forms\Components\Placeholder::make('author_name')
                            ->label('Nama Penulis')
                            ->content(fn(Withdrawal $record): string => $record->user->name),
                        Forms\Components\TextInput::make('coins_redeemed')
                            ->disabled()
                            ->label('Koin yang Dicairkan')
                            ->numeric(),
                        Forms\Components\TextInput::make('amount_rupiah')
                            ->disabled()
                            ->label('Nominal Transfer (Rp)')
                            ->prefix('Rp')
                            ->numeric(),

                        Forms\Components\TextInput::make('bank_name')->disabled()->label('Nama Bank'),
                        Forms\Components\TextInput::make('bank_account_number')->disabled()->label('No. Rekening'),
                        Forms\Components\TextInput::make('bank_account_name')->disabled()->label('Atas Nama'),
                    ])->columns(2),

                Forms\Components\Section::make('Ruang Eksekusi Finance')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Menunggu Diproses',
                                'approved' => 'Selesai Ditransfer',
                                'rejected' => 'Ditolak / Gagal',
                            ])
                            ->required()
                            ->label('Status Pencairan')
                            ->dehydrated(false), // 🌟 FIX!

                        Forms\Components\FileUpload::make('proof_image')
                            ->label('Upload Bukti Transfer')
                            ->directory('withdrawals/proofs')
                            ->image()
                            ->dehydrated(false), // 🌟 FIX!

                        Forms\Components\Textarea::make('finance_notes')
                            ->label('Catatan Finance (Wajib diisi jika ditolak)')
                            ->placeholder('Contoh: Nomor rekening tidak ditemukan...')
                            ->columnSpanFull()
                            ->dehydrated(false), // 🌟 FIX!
                    ])->color('success'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->label('Penulis'),
                Tables\Columns\TextColumn::make('amount_rupiah')
                    ->money('IDR')
                    ->sortable()
                    ->label('Nominal (Rp)'),
                Tables\Columns\TextColumn::make('bank_name')->label('Bank'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->label('Tanggal Pengajuan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu Diproses',
                        'approved' => 'Selesai',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Proses'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'edit' => Pages\EditWithdrawal::route('/{record}/edit'),
        ];
    }
}
