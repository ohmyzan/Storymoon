<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Settings\GeneralSettings;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // 1. Ambil semua pengaturan dari database (Asumsi model GlobalSetting berbasis Key-Value)
        // Cache data ini di Production agar database tidak jebol!
        /** @var array<string, mixed> $settings */
        $settings = cache()->remember('general_settings', 60 * 24, function (): array {
            return app(GeneralSettings::class)->toArray();
        }) ?? [];

        return [
            ...parent::share($request),

            // 2. Data User (Siapa yang sedang login)
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    // Cek apakah dia punya role Author/Editor dll
                    'roles' => $request->user()->getRoleNames(),
                ] : null,
            ],

            // 3. Data Global Config (Mengalir ke SEMUA komponen React seperti Navbar & Footer)
            'globalConfig' => [
                'announcement_text' => $settings['announcement_text'] ?? null,
                'is_maintenance' => $settings['maintenance_mode'] ?? false,
                'seo' => [
                    'description' => $settings['meta_description'] ?? 'Platform Web Novel Modern Terbaik.',
                ],
                'social' => [
                    'instagram' => $settings['instagram_url'] ?? '#',
                    'discord' => $settings['discord_url'] ?? '#',
                    'email' => $settings['support_email'] ?? 'support@storymoon.com',
                ]
            ],

            // 4. Flash Messages (Untuk notifikasi sukses Top Up / Beli Koin)
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
        ];
    }
}
