<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\LocationSInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationSService
{
    public function __construct(
        protected LocationSInterface $repository,
    ) {}

    public function getAllDepartments(): Collection
    {
        return $this->repository->getAllDepartments();
    }

    public function getProvinces(int $departmentId): Collection
    {
        try {
            return $this->repository->getProvinces($departmentId);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }

    public function getDistricts(int $provinceId): Collection
    {
        try {
            return $this->repository->getDistricts($provinceId);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
