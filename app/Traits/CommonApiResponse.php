<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait CommonApiResponse
{
    /**
     * Common API response for controllers
     * @param bool $status
     * @param $message
     * @param $result
     * @param $code
     * @return JsonResponse
     */
    public function commonResponse(bool $status, $message, $result, $code): JsonResponse
    {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'result'  => $result
        ], $code);
    }
}
