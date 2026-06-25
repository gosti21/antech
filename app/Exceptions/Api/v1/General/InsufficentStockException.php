<?php

namespace App\Exceptions\Api\v1\General;

use Exception;
use Illuminate\Http\JsonResponse;

class InsufficentStockException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Stock insuficiente'
        ], 400);
    }
}
