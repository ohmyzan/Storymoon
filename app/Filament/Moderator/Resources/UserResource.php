<?php

namespace App\Filament\Moderator\Resources;

use App\Filament\Moderator\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen Pengguna';
    protected static ?string $modelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar Pengguna')
                    ->description('Data ini bersifat Read-Only untuk Moderator.')
                    ->schema([
                        Forms\Components\TextInput::make('name')->disabled(),
                        Forms\Components\TextInput::make('email')->disabled(),
                        Forms\Components\Placeholder::make('roles')
                            ->label('Role Saat Ini')
                            ->content(fn(User $record): string => $record->getRoleNames()->implode(', ')),
                    ])->columns(2),

                Forms\Components\Section::make('Tindakan Kedisiplinan')
                    ->description('Tentukan durasi hukuman bagi pengguna ini.')
                    ->schema([
                        Forms\Components\DateTimePicker::make('muted_until')
                            ->label('Dilarang Komentar (Mute) Sampai')
                            ->helperText('Kosongkan jika tidak ingin di-mute.'),
                        Forms\Components\DateTimePicker::make('suspended_until')
                            ->label('Tangguhkan Akses (Suspend) Sampai')
                            ->helperText('Pengguna tidak bisa mengakses web sementara.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Role')
                    ->colors(['primary']),

                // Indikator Hukuman Aktif
                Tables\Columns\IconColumn::make('is_muted')
                    ->label('Muted')
                    ->boolean()
                    ->getStateUsing(fn(User $record): bool => $record->muted_until && $record->muted_until->isFuture()),

                Tables\Columns\IconColumn::make('is_suspended')
                    ->label('Suspended')
                    ->boolean()
                    ->getStateUsing(fn(User $record): bool => $record->suspended_until && $record->suspended_until->isFuture()),

                Tables\Columns\TextColumn::make('suspended_until')
                    ->label('Suspend Hingga')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter untuk mencari siapa saja yang sedang dihukum
                Tables\Filters\Filter::make('currently_suspended')
                    ->label('Sedang Disuspensi')
                    ->query(fn(Builder $query): Builder => $query->where('suspended_until', '>', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Kelola Akses'),
            ])
            ->bulkActions([]);
    }

    // 🔒 PENTING: Moderator HANYA BOLEH melihat/mengelola user dengan role 'Author' dan 'Reader'
    // Mereka TIDAK BOLEH mengelola Admin, Editor, atau sesama Moderator.
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('roles') // 🌟 Tarik peran (Role) sekaligus!
            ->role(['Author', 'Reader']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // Kita matikan tombol Create karena user baru hanya dari pendaftaran
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
