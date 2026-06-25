<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Http\Controllers\Controller;
use App\Services\Api\v1\Shop\LocationSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationSController extends Controller
{
    public function __construct(
        protected LocationSService $service
    ) {}

    public function getAllDepartments(): JsonResponse
    {
        $departments = $this->service->getAllDepartments();
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $departments,
        ], 200);
    }

    public function getProvinces(int $departmentId): JsonResponse
    {
        $provinces = $this->service->getProvinces($departmentId);
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $provinces,
        ], 200);
    }

    public function getDistricts(int $provinceId): JsonResponse
    {
        $districts = $this->service->getDistricts($provinceId);
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $districts,
        ], 200);
    }
}
