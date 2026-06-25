<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Services\Api\v1\Admin\BaseService;
use Illuminate\Http\JsonResponse;

/**
 * @template T of BaseService
 */
abstract class BaseController extends Controller
{
    /**
     * @var T
     */
    protected $service;

    /**
     * @param T $service
     */
    public function __construct(
        BaseService $service,
        protected string $resourceClass
    ) {
        $this->service = $service;
    }

    //aqui deberia pasar el paginate
    public function index(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = $this->resourceClass::collection(
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

    public function getAllList(): JsonResponse
    {
        $array = $this->resourceClass::collection(
            $this->service->getAllList()
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $array['data'],
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $model = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new ($this->resourceClass)($model),
        ], 200);
    }
}
