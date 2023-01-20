<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public static function response($data = null, $status, $message = null)
    {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }
}
