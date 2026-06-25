<?php

namespace App\Exceptions\Api\v1;

use Exception;
use Illuminate\Http\JsonResponse;

class NotFoundException extends Exception
{
    public function render () : JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Recurso no encontrado'
        ], 404);
    }
}
