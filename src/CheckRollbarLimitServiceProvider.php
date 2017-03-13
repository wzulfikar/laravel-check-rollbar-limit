<?php

namespace Wzulfikar\CheckRollbarLimit;

use Illuminate\Support\ServiceProvider;

class CheckRollbarLimitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('rollbar:check-limit', function ($app) {
            return new CheckRollbarLimitCommand();
        });

        $this->commands([
            'rollbar:check-limit',
        ]);
    }
}
