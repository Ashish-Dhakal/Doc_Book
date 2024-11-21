<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Policies\AppointmentPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

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
