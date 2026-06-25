<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Option\StoreOptionRequest;
use App\Http\Requests\Api\v1\Admin\Option\UpdateOptionRequest;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Admin\OptionResource;
use App\Http\Resources\Api\v1\Admin\OptionShortResource;
use App\Services\Api\v1\Admin\OptionService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<OptionService>
 */
class OptionController extends BaseController
{
    public function __construct(OptionService $service)
    {
        parent::__construct($service, OptionResource::class);
    }

    public function index(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = OptionShortResource::collection(
            $this->service->getAll($perPage)
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado paginado exitoso',
            'data' => $array['data'],
            'links' => $array['links'],
            'meta' => $array['meta'],
        ], 200);
    }

    public function store(StoreOptionRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new OptionResource($response),
        ], 201);
    }

    public function update(UpdateOptionRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new OptionResource($model),
        ], 200);
    }

    public function getOptionValues(int $id)
    {
        $optionValues = $this->service->getOptionValues($id);
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $optionValues,
        ], 200);
    }
}
