<?php

namespace Wergh\RemoteApiLogin\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Passport\RefreshToken;

class CreatePassportTokenService
{

    public $authenticatableInstance;

    public function __construct($authenticatableInstance)
    {
        $this->authenticatableInstance = $authenticatableInstance;
    }

    public function createToken(): array
    {
        $tokenParams = Config::get('remote-api-login.returned_params');

        $tokenResult = $this->authenticatableInstance->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $returnParams[$tokenParams['access_token']] = $tokenResult->accessToken;

        if (array_key_exists('refresh_token', $tokenParams)) {
            $refreshToken = RefreshToken::create([
                'id' => Str::uuid(), // ID único para el refresh token
                'access_token_id' => $token->id,
                'revoked' => false,
                'expires_at' => Carbon::now()->addDays((int)Config::get('remote-api-login.refresh_token_expiration_time')),
            ]);
            $returnParams[$tokenParams['refresh_token']] = $refreshToken->id;
        }

        if (array_key_exists('expires_in', $tokenParams)) {
            $returnParams[$tokenParams['expires_in']] = Carbon::now()->addDays((int)Config::get('remote-api-login.access_token_expiration_time'));
        }
        return $returnParams;
    }
}
