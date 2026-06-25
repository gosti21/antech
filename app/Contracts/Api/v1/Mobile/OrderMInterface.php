<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface OrderMInterface
{
    public function getAll(int $pagination): LengthAwarePaginator;
    public function getTotals(): array;
    public function getById(int $id): Model;
    public function create(array $orderData): Model;
    public function createDetails(Model $order, array $items): void;
    public function attachPaymentMethod(Model $order, int $paymentMethodId, array $paymentData): void;
}
