<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\MethodPaymentMInterface;
use Illuminate\Database\Eloquent\Model;

class MethodPaymentMService
{
    public function __construct(
        protected MethodPaymentMInterface $repository
    ) {}

    public function getYape(): Model
    {
        return $this->repository->getYape();
    }

    public function getPlin(): Model
    {
        return $this->repository->getPlin();
    }
}
