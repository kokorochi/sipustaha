<?php

namespace App\Providers;

use App\UserAuth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-menu', function ($user)
        {
            return (
                UserAuth::where('username', $user->username)
                    ->where('auth_type', 'SU')
                    ->exists()
            );
        });

        Gate::define('approval-menu', function ($user)
        {
            return (
                UserAuth::where('username', $user->username)->where(function ($query) {
                    $query->where('auth_type', 'OPEL')->orWhere('auth_type', 'OWR3')->orWhere('auth_type', 'SU');
                })->exists()
            );
        });
    }
}
