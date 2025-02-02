<?php

namespace Wergh\RemoteApiLogin\Services;

use Wergh\RemoteApiLogin\Entities\RemoteApiLogin;

class RequestApiLoginService
{

    public function newRequest(): RemoteApiLogin
    {
        return RemoteApiLogin::create(RemoteApiLogin::newData());
    }


}
