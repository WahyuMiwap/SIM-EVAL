<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function ok(mixed $data = null, string $message = 'Berhasil.'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ]);
    }

    public static function fail(string $message, mixed $errors = null, int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }

    public static function denied(string $message = 'Akses ditolak untuk peran Anda.'): JsonResponse
    {
        return self::fail($message, null, 403);
    }

    public static function notFound(string $message = 'Data tidak ditemukan.'): JsonResponse
    {
        return self::fail($message, null, 404);
    }
}
