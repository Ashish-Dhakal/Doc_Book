<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('admin_access', function ($user) {
            return $user->roles === 'admin';
        });

        Gate::define('patient_access', function ($user) {
            return $user->roles === 'patient';
        });

        Gate::define('doctor_access', function ($user) {
            return $user->roles === 'doctor';
        });
    }
}
