<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Return standardized success JSON response.
     */
    public function sendResponse(mixed $data = null, string $message = 'Success', int $code = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Return standardized error JSON response.
     */
    public function sendError(string $message = 'Error', array|string $errors = [], int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = is_array($errors) ? $errors : ['error' => $errors];
        }

        return response()->json($response, $code);
    }

    /**
     * Return unauthorized response.
     */
    public function sendUnauthorized(string $message = 'Unauthenticated or invalid token'): JsonResponse
    {
        return $this->sendError($message, [], 401);
    }
}