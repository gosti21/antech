<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Mobile\MethodPaymentMResource;
use App\Services\Api\v1\Mobile\MethodPaymentMService;
use Illuminate\Http\JsonResponse;

class MethodPaymentMController extends Controller
{
    public function __construct(
        protected MethodPaymentMService $service
    ) {}

    public function getYape(): JsonResponse
    {
        $model = $this->service->getYape();

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => new MethodPaymentMResource($model),
        ], 200);
    }

    public function getPlin(): JsonResponse
    {
        $model = $this->service->getPlin();
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => new MethodPaymentMResource($model),
        ], 200);
    }
}
