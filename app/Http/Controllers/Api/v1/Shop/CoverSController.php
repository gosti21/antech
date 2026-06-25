<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Shop\CoverSResource;
use App\Services\Api\v1\Shop\CoverSService;
use Illuminate\Http\JsonResponse;

class CoverSController extends Controller
{
    public function __construct(
        protected CoverSService $service
    ) {}

    public function getAll(): JsonResponse
    {
        $response = CoverSResource::collection(
            $this->service->getAll()
        );

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $response,
        ], 200);
    }
}
