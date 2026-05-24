<?php

namespace App\Filament\Moderator\Resources;

use App\Filament\Moderator\Resources\KeywordFilterResource\Pages;
use App\Models\KeywordFilter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KeywordFilterResource extends Resource
{
    protected static ?string $model = KeywordFilter::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationLabel = 'Sensor Kata';

    protected static ?string $modelLabel = 'Filter Kata';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Aturan Sensor Komentar')
                    ->description('Masukkan kata terlarang dan kata penggantinya saat ditampilkan di frontend.')
                    ->schema([
                        Forms\Components\TextInput::make('keyword')
                            ->label('Kata Terlarang / Kasar')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: b*doh'),

                        Forms\Components\TextInput::make('replacement')
                            ->label('Ganti Menjadi (Opsional)')
                            ->default('***')
                            ->required()
                            ->placeholder('Contoh: *** atau (disensor)'),

                        // 🌟 FIX DARI CLAUDE: Tambahkan field Severity yang hilang
                        Forms\Components\Select::make('severity')
                            ->options([
                                'low' => 'Rendah (Misal: Umpatan ringan)',
                                'medium' => 'Sedang (Misal: Kasar)',
                                'high' => 'Tinggi (Misal: SARA/Pornografi)',
                            ])
                            ->default('medium')
                            ->required()
                            ->label('Tingkat Keparahan'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Sensor Ini')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Tarik data moderator pembuatnya
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->with('creator'))
            ->columns([
                Tables\Columns\TextColumn::make('keyword')
                    ->searchable()
                    ->sortable()
                    ->label('Kata Terlarang')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('replacement')
                    ->label('Diganti Menjadi')
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status Aktif'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->label('Tanggal Masuk'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Filter')
                    ->boolean(),
            ])
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
            'index' => Pages\ListKeywordFilters::route('/'),
            'create' => Pages\CreateKeywordFilter::route('/create'),
            'edit' => Pages\EditKeywordFilter::route('/{record}/edit'),
        ];
    }
}
