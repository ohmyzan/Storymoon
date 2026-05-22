<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

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
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name', function (Builder $query) {
                                return $query->whereNotIn('name', ['super_admin', 'Finance']);
                            })
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
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label('Role'),

                // STATUS BANNED
                Tables\Columns\IconColumn::make('is_banned')
                    ->getStateUsing(fn(User $record) => $record->banned_at !== null)
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->label('Banned'),

                // STATUS VERIFIED AUTHOR
                Tables\Columns\IconColumn::make('is_verified')
                    ->getStateUsing(fn(User $record) => $record->author_verified_at !== null)
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->label('Verified Author'),
            ])
            ->actions([

                Tables\Actions\EditAction::make(),

                // VERIFIKASI AUTHOR
                Action::make('verify')
                    ->label(fn(User $record) => $record->author_verified_at ? 'Unverify' : 'Verify Author')
                    ->icon('heroicon-o-check-badge')
                    ->color('info')
                    ->visible(fn(User $record) => $record->hasRole('Author'))
                    ->action(function (User $record) {
                        $record->update([
                            'author_verified_at' => $record->author_verified_at ? null : now(),
                        ]);
                    }),

                // VERIFIKASI EDITOR
                Action::make('verify_editor')
                    ->label(fn(User $record) => $record->editor_verified_at ? 'Unverify Editor' : 'Verify Editor')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->visible(fn(User $record) => $record->hasRole('Editor'))
                    ->action(function (User $record) {
                        $record->update([
                            'editor_verified_at' => $record->editor_verified_at ? null : now(),
                        ]);
                    }),

                // BAN / UNBAN USER
                Action::make('ban_user')
                    ->label(fn(User $record) => $record->banned_at ? 'Unban' : 'Ban User')
                    ->icon(fn(User $record) => $record->banned_at
                        ? 'heroicon-o-arrow-path'
                        : 'heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update([
                            'banned_at' => $record->banned_at ? null : now(),
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // 🌟 PERBAIKAN: Kunci visibilitas agar Admin tidak bisa menyentuh Super Admin dan Finance
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['super_admin', 'Finance']);
            })
            ->with('roles'); // Bonus: Sekaligus mencegah N+1 Query Problem!
    }
}
