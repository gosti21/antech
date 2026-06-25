<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Model;

interface OrderSInterface
{
    public function getById(int $id): ?Model;
    public function create(array $orderData): Model;
    public function createDetails(Model $order, array $items): void;
    public function attachPaymentMethod(Model $order, int $paymentMethodId, array $paymentData): void;
    public function validateStock(Model $order): array;
    public function markAsPaid(Model $order, int $customerId): void;
}
