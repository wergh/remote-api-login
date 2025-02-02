<?php

namespace Wergh\RemoteApiLogin\Providers;

use Illuminate\Support\ServiceProvider;
use Wergh\RemoteApiLogin\Classes\RemoteApiLogin;
use Wergh\RemoteApiLogin\Events\RemoteApiLoginSendLoginSuccessfullEvent;

class RemoteApiLoginServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/remote-api-login.php' => config_path('remote-api-login.php'),
        ], 'remote-api-login-config');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->app['events']->listen(
            RemoteApiLoginSendLoginSuccessfullEvent::class,
            function ($event) {
            }
        );

    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/remote-api-login.php', 'remote-api-login'
        );

    }

}
