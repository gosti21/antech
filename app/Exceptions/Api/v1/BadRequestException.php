<?php

namespace App\Exceptions\Api\v1;

use Exception;
use Illuminate\Http\JsonResponse;

class BadRequestException extends Exception
{
    protected $message;
    protected $details;
    protected $code = 400;

    /**
     * Constructor de la excepción BadRequest
     *
     * @param string $message Mensaje principal del error
     * @param string|null $details Detalles adicionales opcionales
     */
    public function __construct(string $message = 'Bad Request', ?string $details = null)
    {
        $this->message = $message;
        $this->details = $details;
        parent::__construct($message, $this->code);
    }

    /**
     * Renderizar la excepción como respuesta JSON
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->message,
        ];

        if ($this->details) {
            $response['details'] = $this->details;
        }

        return response()->json($response, $this->code);
    }
}
