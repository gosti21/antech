<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Mobile\CategoryMResource;
use App\Services\Api\v1\Mobile\CategoryMService;
use Illuminate\Http\JsonResponse;

class CategoryMController extends Controller
{
    public function __construct(
        protected CategoryMService $service
    )
    { }

    public function getAllList(): JsonResponse
    {
        $response = CategoryMResource::collection(
            $this->service->getAllList()
        );

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $response,
        ], 200);
    }

    public function getSubcategories(int $id)
    {
        $subcategories = $this->service->getSubcategories($id);
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $subcategories,
        ], 200);
    }
}
