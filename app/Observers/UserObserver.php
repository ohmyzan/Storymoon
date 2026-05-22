<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Menangani event "created" pada model User.
     * Dipicu otomatis setelah user sukses mendaftar/dibuat.
     */
    public function created(User $user): void
    {
        // Gembok proses dengan DB transaction demi keamanan tingkat tinggi
        DB::transaction(function () use ($user) {
            $user->wallet()->create([
                'coin_balance' => 0,
                'revenue_balance' => 0.00,
            ]);
        });

        // Opsional: Catat log sistem untuk kebutuhan audit internal backend
        Log::info("Dompet digital berhasil dibuat otomatis untuk User ID: {$user->id}");
    }
}
