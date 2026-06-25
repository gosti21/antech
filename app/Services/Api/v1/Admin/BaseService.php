<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\BaseInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @template T of BaseInterface
 */
abstract class BaseService
{
    /**
     * @var T
     */
    protected $repository;

    /**
     * @param T $repository
     */
    public function __construct(
        BaseInterface $repository,
    ){
        $this->repository = $repository;
    }

    public function getAll(int $pagination = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($pagination);
    }

    public function getAllList(): Collection
    {
        return $this->repository->getAllList();
    }

    public function getById(int $id): ?Model
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }
}
