<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class BookingException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => []
        ], 422);
    }
}
