<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Manajemen Konten';
    protected static ?string $modelLabel = 'Banner Promosi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Banner')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Banner (Internal)'),

                        Forms\Components\TextInput::make('target_url')
                            ->url()
                            ->maxLength(255)
                            ->label('URL Tujuan (Link saat diklik)'),

                        Forms\Components\FileUpload::make('image_path')
                            ->image()
                            ->required()
                            ->directory('banners')
                            ->columnSpanFull()
                            ->label('Gambar Spanduk (Disarankan rasio lebar 3:1, cth: 1200x400px)'),

                        // [FIX] Menambahkan field jadwal tayang dari migrasi baru
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Tayang Mulai')
                            ->nullable(),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Tayang Sampai')
                            ->after('start_date') // Validasi bawaan Laravel
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Tayangkan Banner Ini?'),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->label('Urutan Tampil (Makin kecil makin awal)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Preview'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Judul'),
                // [FIX] Menampilkan jadwal di tabel
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('d M Y')
                    ->label('Mulai')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime('d M Y')
                    ->label('Selesai')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->label('Urutan'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif'),
            ])
            ->reorderable('sort_order')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
