<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\ShippingCompanyInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @extends BaseService<ShippingCompanyInterface>
 */
class ShippingCompanyService extends BaseService
{
    public function __construct(ShippingCompanyInterface $repository)
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
}
