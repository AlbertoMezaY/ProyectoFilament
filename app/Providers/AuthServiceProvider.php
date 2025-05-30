<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Definimos un gate para permitir ver la sección Roles solo al Super Admin
        Gate::define('viewRoles', function ($user) {
            return $user->hasRole('Super Admin');
        });
    }
}