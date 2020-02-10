<?php

namespace App\Providers;

use Laravel\Passport\Passport;
//use Illuminate\Support\Facades\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

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
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
        $gate->define('isAdmin', function ($user){
            return $user->user_type == 1;
        });
        $gate->define('isOperator', function ($user){
            return $user->user_type == 2;
        });
        $gate->define('isUser', function ($user){
            return $user->user_type == 3;
        });
        $gate->define('isGuest', function ($user){
            return $user->user_type == 4;
        });

        Passport::routes();
    }
}
