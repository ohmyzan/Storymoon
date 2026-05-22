<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\ActivityResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Builder;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    // Ganti ikon menjadi mata / radar pengintai
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Audit Logs';
    protected static ?string $modelLabel = 'Rekaman Aktivitas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make('Detail Pelaku & Waktu')
                        ->schema([
                            Forms\Components\TextInput::make('causer_id')
                                ->label('Pelaku (ID / Nama)')
                                ->formatStateUsing(fn($record) => $record->causer ? $record->causer->name . ' (ID: ' . $record->causer->id . ')' : 'Sistem Otomatis')
                                ->disabled(),
                            Forms\Components\TextInput::make('created_at')
                                ->label('Waktu Kejadian')
                                ->formatStateUsing(fn($record) => $record->created_at->format('d M Y, H:i:s'))
                                ->disabled(),
                        ])->columns(2),

                    Forms\Components\Section::make('Perubahan Data (Properties)')
                        ->schema([
                            Forms\Components\KeyValue::make('properties.old')
                                ->label('Data Lama (Sebelum Diubah)')
                                ->disabled()
                                ->visible(fn($record) => isset($record->properties['old'])),

                            Forms\Components\KeyValue::make('properties.attributes')
                                ->label('Data Baru (Sesudah Diubah)')
                                ->disabled()
                                ->visible(fn($record) => isset($record->properties['attributes'])),
                        ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Pelaku')
                    ->searchable()
                    ->sortable()
                    ->default('Sistem Otomatis')
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('description')
                    ->label('Aksi')
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                    ]),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Modul / Tabel')
                    ->formatStateUsing(fn($state) => class_basename($state)) // Hanya ambil nama modelnya saja (contoh: 'Novel')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID Data')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // Urutkan dari yang paling baru
            ->filters([
                Tables\Filters\SelectFilter::make('description')
                    ->label('Jenis Aksi')
                    ->options([
                        'created' => 'Created (Buat Baru)',
                        'updated' => 'Updated (Edit Data)',
                        'deleted' => 'Deleted (Hapus Data)',
                    ]),
            ])
            ->actions([
                // Cuma ada aksi VIEW, tidak boleh Edit!
                Tables\Actions\ViewAction::make()->label('Lihat Detail'),
            ])
            ->bulkActions([]); // Matikan Bulk Action agar tidak ada yang bisa menghapus log massal
    }

    public static function getPages(): array
    {
        return [
            // Cukup gunakan halaman index (List) saja, kita tidak butuh Create & Edit
            'index' => Pages\ListActivities::route('/'),
        ];
    }

    // PENGUNCIAN MUTLAK: Tidak ada yang boleh menambah, mengedit, atau menghapus Log!
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
