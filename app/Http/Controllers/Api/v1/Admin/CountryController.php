<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Country\StoreCountryRequest;
use App\Http\Requests\Api\v1\Admin\Country\UpdateCountryRequest;
use App\Http\Resources\Api\v1\Admin\CountryResource;
use App\Services\Api\v1\Admin\CountryService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<CountryService>
 */
class CountryController extends BaseController
{
    public function __construct(CountryService $service)
    {
        parent::__construct($service, CountryResource::class);
    }

    public function getAllList(): JsonResponse
    {
        $countries = $this->service->getAllList();

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $countries,
        ], 200);
    }

    public function store(StoreCountryRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new CountryResource($response),
        ], 201);
    }

    public function update(UpdateCountryRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new CountryResource($model),
        ], 200);
    }

    public function getDepartments(int $id)
    {
        $departments = $this->service->getDepartments($id);
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $departments,
        ], 200);
    }
}
