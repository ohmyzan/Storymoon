<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletingScope; // Wajib ditambahkan
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    protected static ?string $modelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(
                                fn($state) => filled($state) ? Hash::make($state) : null
                            )
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Role (Akses)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query->with('roles')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label('Role'),

                Tables\Columns\IconColumn::make('is_banned')
                    ->getStateUsing(
                        fn(User $record) => $record->banned_at !== null
                    )
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->label('Banned'),

                Tables\Columns\IconColumn::make('is_verified_author')
                    ->getStateUsing(
                        fn(User $record) => $record->author_verified_at !== null
                    )
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->label('Verified Author'),
            ])
            ->filters([
                // 🌟 FIX CLAUDE: Tambahkan filter SoftDeletes
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                // 🌟 FIX: Aksi Edit dipindah paling atas, aksi lain dipindah ke EditUser.php
                Tables\Actions\EditAction::make()->label('Kelola User'),

                // IMPERSONATE (Boleh di tabel karena ini aksi cepat Super Admin)
                Impersonate::make()
                    ->color('gray')
                    ->tooltip('Login sebagai user ini'),

                Tables\Actions\DeleteAction::make(),

                // 🌟 FIX CLAUDE: Tambahkan aksi Restore
                Tables\Actions\RestoreAction::make(),
            ]);
    }

    // 🌟 FIX CLAUDE: Nonaktifkan Scope Delete agar data sampah terlihat
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
