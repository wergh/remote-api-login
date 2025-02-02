<?php

namespace Wergh\RemoteApiLogin\Services;

use Illuminate\Support\Facades\Config;

class CreateTokenService
{

    /**
     * @throws \Exception
     */
    public function createToken($authenticatableInstance): array
    {

        $authDriver = Config::get('remote-api-login.auth_driver');

        switch ($authDriver) {
            case 'sanctum':
                $sanctumTokenService = new CreateSanctumTokenService($authenticatableInstance);
                return $sanctumTokenService->createToken();
            case 'passport':
                $passportTokenService = new CreatePassportTokenService($authenticatableInstance);
                return $passportTokenService->createToken();
            case 'custom':
                $customTokenService = new CreateCustomTokenService($authenticatableInstance);
                return $customTokenService->createToken();
            default:
                throw new \Exception('Invalid authentication driver specified.');
        }

    }

}
