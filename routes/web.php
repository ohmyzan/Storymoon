<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Rute Publik (Homepage, Detail Novel)
Route::get('/', function () {
    return view('welcome'); // Nanti diganti ke view homepage Storymoon
})->name('home');

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
