<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Return a standardized JSON success response.
     */
    public function success(string $message = 'Operation successful', mixed $data = [], int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a standardized JSON error response.
     */
    public function error(string $message = 'Operation failed', array|int $errors = [], int $status = 400)
    {
        if (is_int($errors)) {
            $status = $errors;
            $errors = [];
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
