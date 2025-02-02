<?php

namespace Wergh\RemoteApiLogin\Controllers;

use Illuminate\Http\JsonResponse;
use Wergh\RemoteApiLogin\Entities\RemoteApiLogin;
use Wergh\RemoteApiLogin\Requests\GetTokenRequest;
use Wergh\RemoteApiLogin\Services\CreateTokenService;

class ApiGetTokenController extends AppBaseController
{

    public CreateTokenService $createTokenService;

    public function __construct(CreateTokenService $createTokenService)
    {
        $this->createTokenService = $createTokenService;
    }


    public function __invoke(GetTokenRequest $request): JsonResponse
    {

        $remoteApiLogin = RemoteApiLogin::searchByUuidAndToken($request->uuid, $request->token);

        if(!$remoteApiLogin) {
            return $this->errorResponse('Invalid params');
        }

        return $this->successResponse($this->createTokenService->createToken($remoteApiLogin->authenticatable), 'Token retrieved successfully');

    }
}
