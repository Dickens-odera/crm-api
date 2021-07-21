<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait CommonApiResponse
{
    /**
     * Common API response for controllers
     * @param bool $status
     * @param $message
     * @param $data
     * @param int $code
     * @return JsonResponse
     */
    public function commonResponse(bool $status, $message, $data, int $code): JsonResponse
    {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}
