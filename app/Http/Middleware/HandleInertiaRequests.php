<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Support\SettingsCache; // ✅ Tambah ini
// ❌ Hapus: use App\Settings\GeneralSettings;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // ✅ Pakai SettingsCache (clean & centralized)
        $settings = SettingsCache::get() ?? [];

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->getRoleNames(),
                ] : null,
            ],

            'globalConfig' => [
                'announcement_text'   => $settings['announcement_text'] ?? null,
                'is_maintenance'      => $settings['maintenance_mode'] ?? false,
                'maintenance_message' => $settings['maintenance_message'] ?? null,

                'seo' => [
                    'description' => $settings['meta_description'] ?? 'Platform Web Novel Modern Terbaik.',
                ],

                'social' => [
                    'instagram' => $settings['instagram_url'] ?? '#',
                    'discord'   => $settings['discord_url'] ?? '#',
                    'email'     => $settings['support_email'] ?? 'support@storymoon.com',
                ]
            ],

            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error'   => fn() => $request->session()->get('error'),
            ],
        ];
    }
}
