<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LegalContractResource\Pages;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LegalContractResource extends Resource
{
    protected static ?string $model = Contract::class;
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Manajemen Legal & Finansial';
    protected static ?string $navigationLabel = 'Validasi KYC Kontrak';
    protected static ?string $modelLabel = 'Dokumen KYC Kontrak';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar Kontrak')
                    ->schema([
                        Forms\Components\Placeholder::make('novel_title')
                            ->label('Judul Novel')
                            ->content(fn(Contract $record): string => $record->novel->title ?? 'Novel Dihapus'),
                        Forms\Components\Placeholder::make('author_name')
                            ->label('Penulis (Akun)')
                            ->content(fn(Contract $record): string => $record->author->name ?? 'Penulis Dihapus'),
                        Forms\Components\TextInput::make('contract_type')->disabled()->label('Tipe Kontrak'),
                        Forms\Components\TextInput::make('revenue_share_author')->disabled()->label('Bagi Hasil Author (%)'),
                    ])->columns(2),

                Forms\Components\Section::make('Dokumen Legalitas (KYC) - Read Only')
                    ->schema([
                        Forms\Components\TextInput::make('real_name')->disabled()->label('Nama Asli Sesuai KTP'),
                        Forms\Components\TextInput::make('id_card_number')->disabled()->label('Nomor Induk Kependudukan (NIK)'),
                        Forms\Components\TextInput::make('bank_name')->disabled()->label('Bank / E-Wallet'),
                        Forms\Components\TextInput::make('bank_account_number')->disabled()->label('Nomor Rekening'),
                        Forms\Components\TextInput::make('bank_account_name')->disabled()->label('Atas Nama Rekening'),
                        Forms\Components\TextInput::make('external_links')->disabled()->label('Link Eksternal (Bukti Takedown)'),
                    ])->columns(2),

                Forms\Components\Section::make('Lampiran Fisik')
                    ->schema([
                        Forms\Components\FileUpload::make('id_card_image')->disabled()->image()->label('Foto KTP Fisik'),
                        Forms\Components\FileUpload::make('selfie_image')->disabled()->image()->label('Selfie dengan KTP'),
                        // [FIX] Menggunakan nama kolom yang benar sesuai migrasi
                        Forms\Components\FileUpload::make('signature_image_path')->disabled()->image()->label('Coretan Tanda Tangan'),
                    ])->columns(3),

                Forms\Components\Section::make('Ruang Eksekusi Admin (Legal)')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'kyc_review' => 'Sedang Dicek Admin (KYC Review)',
                                'signing' => 'Lolos KYC (Menunggu TTD Digital Penulis)',
                                'active' => 'Kontrak Sah & Aktif!',
                                'rejected' => 'Tolak (Data KYC Palsu / Tidak Sesuai)',
                            ])
                            ->required()
                            ->label('Keputusan Validasi Dokumen')
                            ->dehydrated(false), // [FIX] Dicegat di afterSave untuk menggunakan method advanceTo()

                        Forms\Components\Textarea::make('editor_notes')
                            ->label('Catatan Penolakan (Wajib diisi jika ditolak)')
                            ->placeholder('Contoh: Foto KTP buram...')
                            ->dehydrated(false) // [FIX] Sama seperti status
                            ->columnSpanFull(),
                    ])->color('success'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('novel.title')->searchable()->label('Novel'),
                Tables\Columns\TextColumn::make('real_name')->searchable()->label('Nama Asli (KTP)'),
                Tables\Columns\BadgeColumn::make('contract_type')
                    ->colors(['primary' => 'exclusive', 'success' => 'non_exclusive'])
                    ->label('Tipe Kontrak'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'kyc_review',
                        'primary' => 'signing',
                        'success' => 'active',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'kyc_review' => 'Review KTP',
                        'signing' => 'Menunggu TTD',
                        'active' => 'Aktif',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->label('Status KYC'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->label('Tanggal Pengajuan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Validasi Berkas'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['kyc_review', 'signing', 'active', 'rejected'])
            ->with(['novel', 'author']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLegalContracts::route('/'),
            'edit' => Pages\EditLegalContract::route('/{record}/edit'),
        ];
    }
}
