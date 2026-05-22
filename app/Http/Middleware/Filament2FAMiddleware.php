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

            // Jika dia sudah mengaktifkan 2FA tapi belum verifikasi OTP
            if ($user->google2fa_secret && ! session()->get('2fa_verified')) {

                // Hindari redirect loop
                if (! $request->is('super-admin/verify-2fa')) {
                    return redirect('super-admin/verify-2fa');
                }
            }
        }

        return $next($request);
    }
}
