<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Filament2FAMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Jika user berhasil login dan dia adalah Super Admin
        if ($user && $user->hasRole('super_admin')) {

            // 🔥 pastikan user tidak null di sini
            if (!empty($user->google2fa_secret) && !session('2fa_verified')) {

                if (! $request->is('super_admin/verify-2fa')) {
                    return redirect('super_admin/verify-2fa');
                }
            }
        }

        return $next($request);
    }
}
