<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\ProductInterface;
use App\Exceptions\Api\v1\InternalServerErrorException;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

/**
 * @extends BaseService<ProductInterface>
 */
class ProductService extends BaseService
{
    public function __construct(ProductInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(array $data, int $id): Model
    {
        $productData = Arr::only($data, [
            'name',
            'model',
            'description',
            'status',
            'subcategory_id',
            'brand_id',
        ]);

        $specificationsData = Arr::only($data, ['specifications']);

        try{
            return $this->repository->update($productData, $specificationsData,$id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        } catch (\Exception $e) {
            throw new InternalServerErrorException(
                'No se pudo actualizar el producto',
                $e->getMessage()
            );
        }
    }

    public function getAllOptions(int $id): Model
    {
        try {
            return $this->repository->getAllOptions($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }

    public function hasOptions(int $id): bool
    {
        try {
            return $this->repository->hasOptions($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }

    public function getAllOptionsShort(int $id): Collection
    {
        try {
            return $this->repository->getAllOptionsShort($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }
}
