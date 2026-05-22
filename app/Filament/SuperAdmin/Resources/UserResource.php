<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use PragmaRX\Google2FA\Google2FA;
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
                                fn($state) => filled($state)
                                    ? Hash::make($state)
                                    : null
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

            // Optimasi eager loading
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

            ->actions([

                // EDIT
                Tables\Actions\EditAction::make(),

                // DELETE
                Tables\Actions\DeleteAction::make(),

                // IMPERSONATE
                Impersonate::make()
                    ->color('gray')
                    ->tooltip('Login sebagai user ini'),

                // VERIFY AUTHOR
                Action::make('verify_author')
                    ->label(
                        fn(User $record) =>
                        $record->author_verified_at
                            ? 'Unverify Author'
                            : 'Verify Author'
                    )
                    ->icon('heroicon-o-check-badge')
                    ->color('info')

                    ->visible(
                        fn(User $record) =>
                        $record->hasRole('Author')
                    )

                    ->action(
                        fn(User $record) => $record->update([
                            'author_verified_at' =>
                            $record->author_verified_at
                                ? null
                                : now(),
                        ])
                    ),

                // SETUP 2FA
                Action::make('setup_2fa')

                    ->label(
                        fn(User $record) =>
                        $record->google2fa_secret
                            ? 'Reset 2FA'
                            : 'Aktifkan 2FA'
                    )

                    ->icon('heroicon-o-qr-code')

                    ->color(
                        fn(User $record) =>
                        $record->google2fa_secret
                            ? 'warning'
                            : 'success'
                    )

                    // Hanya user sendiri
                    ->visible(
                        fn(User $record) =>
                        $record->id === Auth::id()
                    )

                    ->form(function (User $record) {

                        $google2fa = app(Google2FA::class);

                        $secret = $google2fa->generateSecretKey();

                        $qrCodeUrl = $google2fa->getQRCodeUrl(
                            config('app.name', 'Storymoon'),
                            $record->email,
                            $secret
                        );

                        // Render QR SVG
                        $renderer = new ImageRenderer(
                            new RendererStyle(250),
                            new SvgImageBackEnd()
                        );

                        $writer = new Writer($renderer);

                        $svg = $writer->writeString($qrCodeUrl);

                        return [

                            Forms\Components\Hidden::make('secret')
                                ->default($secret),

                            Forms\Components\Placeholder::make('qr')
                                ->label('1. Scan QR dengan Google Authenticator')
                                ->content(
                                    new HtmlString(
                                        '<div class="flex justify-center p-4 bg-white rounded-lg">'
                                            . $svg .
                                            '</div>'
                                    )
                                ),

                            Forms\Components\TextInput::make('otp')
                                ->label('2. Masukkan 6 Digit OTP')
                                ->required()
                                ->length(6)
                                ->numeric(),
                        ];
                    })

                    ->action(function (User $record, array $data) {

                        $google2fa = app(Google2FA::class);

                        $valid = $google2fa->verifyKey(
                            $data['secret'],
                            $data['otp']
                        );

                        if ($valid) {

                            $record->update([
                                'google2fa_secret' => $data['secret'],
                            ]);

                            // Session verified
                            request()
                                ->session()
                                ->put('2fa_verified', true);

                            Notification::make()
                                ->title('2FA berhasil diaktifkan!')
                                ->success()
                                ->send();
                        } else {

                            Notification::make()
                                ->title('Kode OTP salah!')
                                ->danger()
                                ->send();
                        }
                    }),

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
