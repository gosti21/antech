<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\UserSInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserSService
{
    public function __construct(
        protected UserSInterface $repository,
    ) {}

    public function getById(int $id): ?Model
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }
}
