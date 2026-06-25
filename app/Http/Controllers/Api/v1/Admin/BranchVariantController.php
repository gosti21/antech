<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Admin\BranchVariantResource;
use App\Services\Api\v1\Admin\BranchVariantService;
use Illuminate\Http\JsonResponse;

class BranchVariantController extends Controller
{
    public function __construct(
        protected BranchVariantService $service
    ) {}

    public function getAllList(): JsonResponse
    {
        $array = BranchVariantResource::collection(
            $this->service->getAllList()
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $array['data'],
        ], 200);
    }
}
