<?php

namespace Wergh\RemoteApiLogin\Services;

use Illuminate\Support\Facades\Config;

class CreateCustomTokenService
{

    public $authenticatableInstance;

    public function __construct($authenticatableInstance)
    {
        $this->authenticatableInstance = $authenticatableInstance;
    }

    /**
     * @throws \Exception
     */
    public function createToken(): array
    {

        $class = Config::get('remote-api-login.custom')['class'];
        $method = Config::get('remote-api-login.custom')['method'];

        if (!class_exists($class) || !method_exists($class, $method)) {
            throw new \Exception('Custom token class or method does not exist.');
        }

        $customService = new $class();
        return $customService->$method($this->authenticatableInstance);
    }

}
