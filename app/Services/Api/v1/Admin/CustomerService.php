<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\CustomerInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        protected CustomerInterface $repository
    ) {}

    public function getAll(int $pagination = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($pagination);
    }
}
