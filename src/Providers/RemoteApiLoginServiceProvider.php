<?php

namespace Wergh\RemoteApiLogin\Providers;

use Illuminate\Support\ServiceProvider;

class NombreDelPaqueteServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // Aquí puedes cargar rutas, vistas, migraciones, etc.
    }

    public function register()
    {
        $this->app->singleton('remote-api-login', function ($app) {
            return new RemoteApiLogin();
        });
    }

}
