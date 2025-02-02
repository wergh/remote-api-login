<?php

namespace Wergh\RemoteApiLogin\Controllers;


use Illuminate\Http\JsonResponse;
use Wergh\RemoteApiLogin\Services\RequestApiLoginService;

class ApiRequestLoginController extends AppBaseController
{

    public RequestApiLoginService $apiLoginService;

    public function __construct(RequestApiLoginService $apiLoginService)
    {
        $this->apiLoginService = $apiLoginService;
    }


    public function __invoke(): JsonResponse
    {

        $data = $this->apiLoginService->newRequest();
         return $this->successResponse([
            'code' => $data->code,
            'temp_user_uuid' => $data->uuid,
            'temp_token' => $data->token,
        ], 'Logged successfully');

    }
}
