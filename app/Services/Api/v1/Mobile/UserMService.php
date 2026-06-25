<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\UserMInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserMService
{
    public function __construct(
        protected UserMInterface $repository,
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
