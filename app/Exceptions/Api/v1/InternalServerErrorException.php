<?php

namespace App\Exceptions\Api\v1;

use Exception;
use Illuminate\Http\JsonResponse;

class InternalServerErrorException extends Exception
{
    public function __construct(
        string $message = 'Ha ocurrido un error en el servidor',
        protected string|null $error = null
    ){
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'error' => $this->error
        ], 500);
    }
}
