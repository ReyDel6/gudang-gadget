<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
        Gate::define('role-admin', fn ($user) => $user !== null && $user->isAdmin());
        Gate::define('role-staff', fn ($user) => $user !== null && ($user->isAdmin() || $user->isStaff()));
    }
}
