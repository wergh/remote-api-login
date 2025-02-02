<?php

namespace Wergh\RemoteApiLogin\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAPI;

trait ApiResponse
{

    public function successResponse(
        ?array  $data = null,
        ?string $message = null,
        int     $status = ResponseAPI::HTTP_OK
    ): JsonResponse
    {
        return Response::json(
            [
                'message' => $message,
                'data' => $data,
            ],
            $status
        );
    }

    public function errorResponse(
        ?string $errorMessage = null,
        int     $status = ResponseAPI::HTTP_INTERNAL_SERVER_ERROR,
        string  $errorCode = 'internal_server_error',
        ?array  $errorData = null
    ): JsonResponse
    {
        return Response::json(
            [
                'errorCode' => $errorCode,
                'errorMessage' => $errorMessage,
                'errorData' => $errorData,
            ],
            $status
        );
    }

}

