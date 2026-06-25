<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\OptionValueInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OptionValueService
{
    public function __construct(
        protected OptionValueInterface $repository
    ){ }

    public function getById(int $id): Model
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }
}
