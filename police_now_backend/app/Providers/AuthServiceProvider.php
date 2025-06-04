<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define a super admin role with full access
        Gate::before(function ($user) {
            $adminRole = $user->role?->role === 'admin';
            if ($adminRole) {
                return true;
            }
        });

        // Define permissions for officers
        Gate::define('manage_emergency_requests', function ($user) {
            return $user->role?->role === 'officer';
        });

        Gate::define('manage_evidence', function ($user) {
            return $user->role?->role === 'officer';
        });

        // Define permissions for residents
        Gate::define('submit_emergency_requests', function ($user) {
            return $user->role?->role === 'resident';
        });
    }
}
