<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // 1. Ambil data user yang sedang login
        $user = $request->user();

        // 2. Logika Redirect Berdasarkan Role
        if ($user->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->intended(route('filament.admin.pages.dashboard'));
        }

        if ($user->hasRole('Editor')) {
            return redirect()->intended(route('filament.editor.pages.dashboard'));
        }

        if ($user->hasRole('Finance')) {
            return redirect()->intended(route('filament.finance.pages.dashboard'));
        }

        if ($user->hasRole('Moderator')) {
            return redirect()->intended(route('filament.moderator.pages.dashboard'));
        }

        // 3. Default Redirect untuk Author dan Reader (ke halaman utama web)
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
