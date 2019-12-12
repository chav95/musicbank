<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Set the timeout for session from settings
        config([
            'session.lifetime' => Config::where('config_name', '=', 'session_lifetime')->value('config_value'),
            'session.expire_on_close' => (Config::where('config_name', '=', 'session_expire_on_close')->value('config_value') == 1 ? true : false),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
