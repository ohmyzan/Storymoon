<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Setiap user baru dibuat → wallet otomatis dibuat.
     * Tidak perlu ingat panggil Wallet::create() di mana pun.
     */
    public function created(User $user): void
    {
        Wallet::create(['user_id' => $user->id]);

        Log::info('Wallet auto-created for new user.', [
            'user_id' => $user->id,
        ]);
    }
}
