<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\SettingsCache;

class CheckMaintenanceMode
{
    protected array $excludedRoutes = [
        'login',
        'logout',
        'password/*',
        'maintenance',
        'api/status', // ✅ Tambahkan ini
        'admin/*',
        'super-admin/*',
        'filament/*',
    ];

    protected array $allowedRoles = [
        'super_admin',
        'Admin',
        'Editor',
        'Moderator',
        'Finance',
    ];

    public function handle(Request $request, Closure $next)
    {
        // ✅ Ambil dari SettingsCache (clean & reusable)
        $settings = SettingsCache::get();

        // Jika maintenance tidak aktif
        if (!($settings['maintenance_mode'] ?? false)) {
            return $next($request);
        }

        // Izinkan route tertentu
        foreach ($this->excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Izinkan user internal
        $user = $request->user();
        if ($user && $user->hasAnyRole($this->allowedRoles)) {
            return $next($request);
        }

        // Redirect ke halaman maintenance
        return redirect()->route('maintenance');
    }
}
