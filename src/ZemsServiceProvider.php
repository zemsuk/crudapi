<?php

namespace Zems\Restapi;

use Illuminate\Support\ServiceProvider;

class ZemsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->make('Zems\Restapi\ZemsController');
        $this->LoadViewsFrom(__DIR__.'/views', 'restapi');
        // $this->LoadViewsFrom(__DIR__.'../../../../resources/views', 'restapi');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
