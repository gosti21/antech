<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Branch\StoreBranchRequest;
use App\Http\Requests\Api\v1\Admin\Branch\UpdateBranchRequest;
use App\Http\Resources\Api\v1\Admin\BranchResource;
use App\Services\Api\v1\Admin\BranchService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<BranchService>
 */
class BranchController extends BaseController
{
    public function __construct(BranchService $service)
    {
        parent::__construct($service, BranchResource::class);
    }

    public function store(StoreBranchRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new BranchResource($response),
        ], 201);
    }

    public function update(UpdateBranchRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new BranchResource($model),
        ], 200);
    }
}
