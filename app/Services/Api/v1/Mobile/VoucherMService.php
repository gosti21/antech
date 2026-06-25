<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\VoucherMInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VoucherMService
{
    public function __construct(
        protected VoucherMInterface $repository,
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
