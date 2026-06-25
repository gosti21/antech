<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\ShippingCompany\StoreShippingCompanyRequest;
use App\Http\Requests\Api\v1\Admin\ShippingCompany\UpdateShippingCompanyRequest;
use App\Http\Resources\Api\v1\Admin\CategoryResource;
use App\Http\Resources\Api\v1\Admin\ShippingCompanyResource;
use App\Services\Api\v1\Admin\ShippingCompanyService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<ShippingCompanyService>
 */
class ShippingCompanyController extends BaseController
{
    public function __construct(ShippingCompanyService $service)
    {
        parent::__construct($service, ShippingCompanyResource::class);
    }

    public function store(StoreShippingCompanyRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new CategoryResource($response),
        ], 201);
    }

    public function update(UpdateShippingCompanyRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new CategoryResource($model),
        ], 200);
    }
}
