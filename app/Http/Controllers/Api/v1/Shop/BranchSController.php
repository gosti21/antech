<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Shop\BranchSResource;
use App\Services\Api\v1\Shop\BranchSService;
use Illuminate\Http\JsonResponse;

class BranchSController extends Controller
{
    public function __construct(
        protected BranchSService $service
    ) {}

    public function getAll(): JsonResponse
    {
        $response = BranchSResource::collection(
            $this->service->getAll()
        );

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $response,
        ], 200);
    }
}
