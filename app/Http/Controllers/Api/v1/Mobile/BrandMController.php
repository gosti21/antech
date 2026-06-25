<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Mobile\BrandMResource;
use App\Services\Api\v1\Mobile\BrandMService;
use Illuminate\Http\JsonResponse;

class BrandMController extends Controller
{
    public function __construct(
        protected BrandMService $service
    ) {}

    public function getAllList(): JsonResponse
    {
        $response = BrandMResource::collection(
            $this->service->getAllList()
        );

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $response,
        ], 200);
    }
}
