<?php

namespace Wergh\RemoteApiLogin\Services;

use Illuminate\Support\Facades\Config;

class CreateSanctumTokenService
{

    public $authenticatableInstance;

    public function __construct($authenticatableInstance)
    {
        $this->authenticatableInstance = $authenticatableInstance;
    }

    public function createToken(): array
    {
        $tokenParams = Config::get('remote-api-login.returned_params');

        $tokenResult = $this->authenticatableInstance->createToken('Personal Access Token')->plainTextToken;

        $returnParams[$tokenParams['access_token']] = $tokenResult;

        return $returnParams;
    }
}
