<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Variant\StoreVariantRequest;
use App\Http\Requests\Api\v1\Admin\Variant\UpdateVariantRequest;
use App\Http\Resources\Api\v1\Admin\VariantResource;
use App\Http\Resources\Api\v1\Admin\VariantShortResource;
use App\Services\Api\v1\Admin\VariantService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<VariantService>
 */
class VariantController extends BaseController
{
    public function __construct(VariantService $service)
    {
        parent::__construct($service, VariantResource::class);
    }

    public function getAllShort(string $id): JsonResponse
    {
        $array = VariantShortResource::collection(
            $this->service->getAllShort($id)
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado paginado exitoso',
            'data' => $array['data'],
            'links' => $array['links'],
            'meta' => $array['meta'],
        ], 200);
    }

    public function store(StoreVariantRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new VariantResource($response),
        ], 201);
    }

    public function update(UpdateVariantRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new VariantResource($model),
        ], 200);
    }
}
