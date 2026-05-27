<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use App\Support\SettingsCache; // ✅ Tambahkan ini

// Rute Publik (Homepage, Detail Novel)
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/maintenance', function () {
    return Inertia::render('Maintenance');
})->name('maintenance');

// ✅ Endpoint status maintenance (DITAMBAHKAN DI SINI)
Route::get('/api/status', function () {
    $settings = SettingsCache::get();

    return response()->json([
        'maintenance' => $settings['maintenance_mode'] ?? false,
    ]);
})->name('status.check');

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/api/genres/{slug}/novels', [HomeController::class, 'loadMoreNovels']);
    Route::get('/api/editor-choices', [HomeController::class, 'getEditorChoices']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:Author'])->prefix('author')->name('author.')->group(function () {
        Route::get('/dashboard', function () {
            return 'Selamat datang di Pusat Penulis'; // Placeholder
        })->name('dashboard');
    });

    // Panel Editor
    Route::get('/editor/dashboard', function () {
        return view('editor.dashboard');
    })->middleware(['auth', 'role:Editor']);
});

require __DIR__ . '/auth.php';
