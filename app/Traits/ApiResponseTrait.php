<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Send a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message,
            'data' => $errors,
        ], $code);
    }
}
