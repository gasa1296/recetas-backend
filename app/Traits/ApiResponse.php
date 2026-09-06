<?php

namespace App\Traits;

trait ApiResponse
{
    public function success($message = 'Operation successful', $data = [], int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function error($message = 'Operation failed', $data = [], int $status = 400)
    {
        if (is_int($data) && $status === 400) {
            $status = $data;
            $data = [];
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $data,
        ], $status);
    }
}
