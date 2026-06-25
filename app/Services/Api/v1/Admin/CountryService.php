<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\CountryInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @extends BaseService<CountryInterface>
 */
class CountryService extends BaseService
{
    public function __construct(CountryInterface $repository)
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
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }

    public function getDepartments(int $id): Collection
    {
        try {
            return $this->repository->getDepartments($id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
