<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\SubcategoryInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @extends BaseService<SubcategoryInterface>
 */
class SubcategoryService extends BaseService
{
    public function __construct(SubcategoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(array $data, int $id): Model
    {
        try {
            return $this->repository->update($data, $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }
}
