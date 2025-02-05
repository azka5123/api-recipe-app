<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use JsonSerializable;

class ResponseHelper
{
    public static function success(string $message, JsonSerializable|array $data, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
