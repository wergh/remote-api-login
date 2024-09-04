<?php

namespace Wergh\RemoteApiLogin\Facades;

use Illuminate\Support\Facades\Facade;

class RemoteApiLogin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'remote-api-login';
    }
}
