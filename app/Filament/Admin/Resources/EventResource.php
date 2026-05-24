<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\EventResource\RelationManagers;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Manajemen Event';
    protected static ?string $modelLabel = 'Lomba';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Lomba')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->label('Nama Lomba'),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->unique(Event::class, 'slug', ignoreRecord: true),

                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->label('Deskripsi & Syarat Lomba'),

                        Forms\Components\FileUpload::make('banner_image')
                            ->image()
                            ->directory('event-banners')
                            ->columnSpanFull()
                            ->label('Banner Lomba'),
                    ])->columns(2),

                Forms\Components\Section::make('Jadwal Pelaksanaan')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->required()
                            ->label('Tanggal Mulai'),

                        // [FIX] Tambahan minDate agar UI kalender tidak bisa memilih hari sebelum start_date
                        Forms\Components\DateTimePicker::make('end_date')
                            ->required()
                            ->after('start_date')
                            ->minDate(fn(Forms\Get $get) => $get('start_date'))
                            ->label('Tanggal Selesai'),

                        // [FIX] Hapus Select 'status' dari sini karena status dihapus dari $fillable Model.
                        // Pengubahan status sekarang dilakukan via Action Button di Tabel.
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Lomba'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'primary' => 'completed',
                    ]),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->label('Mulai'),

                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->label('Selesai'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'completed' => 'Selesai',
                    ]),
            ])
            ->actions([
                // [FIX] Action baru untuk mengubah status karena form biasa ditutup
                Tables\Actions\Action::make('publish')
                    ->label('Aktifkan Lomba')
                    ->icon('heroicon-o-megaphone')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Event $record) => $record->status === 'draft')
                    ->action(function (Event $record) {
                        // Tidak menggunakan update() array agar aman dari blokir fillable
                        $record->status = 'active';
                        $record->save();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ParticipantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
