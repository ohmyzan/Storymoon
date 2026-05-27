<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🛡️ GOD MODE:
        // Super Admin otomatis bypass seluruh permission/policy
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('super_admin')
                ? true
                : null;
        });

        // 👀 Observer User
        User::observe(UserObserver::class);
    }
}
