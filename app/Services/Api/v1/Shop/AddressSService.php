<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\AddressSInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddressSService
{
    public function __construct(
        protected AddressSInterface $repository,
        protected UserSService $user_sservice
    ) {}

    public function getAll(int $userId): Collection
    {
        $user = $this->user_sservice->getById($userId);
        return $this->repository->getAll($user->id);
    }

    public function getById(int $id): Model
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }

    public function favorite(int $userId): ?Model
    {
        $user = $this->user_sservice->getById($userId);
        return $this->repository->favorite($user->id);
    }

    public function create(array $data, int $userId): Model
    {
        $user = $this->user_sservice->getById($userId);
        return $this->repository->create($data, $user);
    }

    public function update(array $data, int $id): Model
    {
        try {
            return $this->repository->update($data, $id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }

    public function delete(int $id): bool
    {
        try {
            return $this->repository->delete($id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
