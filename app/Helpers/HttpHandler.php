<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

final class HttpHandler
{
    /**
     * API success response
     *
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function successResponse(mixed $data, int $statusCode = JsonResponse::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data'       => $data,
            'status'     => Constants::SUCCESS
        ], $statusCode);
    }

    /**
     * API success message response
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function successMessage(string $message, int $statusCode = JsonResponse::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data' => [
                'message' => $message,
            ],
            'status' => Constants::SUCCESS
        ], $statusCode);
    }

    /**
     * API error message response
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function errorResponse(string $message, int $statusCode = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'data'    => null,
            'message' => $message,
            'status'  => Constants::FAILED
        ], $statusCode);
    }

    /**
     * API error message response
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function errorMessage(string $message, int $statusCode = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status'  => Constants::FAILED
        ], $statusCode);
    }

    public static function errorLogMessageHandler(string $message, int $statusCode = JsonResponse::HTTP_BAD_REQUEST): void
    {
        Log::error("Message: $message, Status Code: $statusCode");
    }

    public static function errorHandler(string $message, array $data = []): void
    {
        Log::error("$message: ", $data);
    }

}
