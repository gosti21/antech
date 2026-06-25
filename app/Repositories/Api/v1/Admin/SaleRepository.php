<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\SaleInterface;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class SaleRepository implements SaleInterface
{
    public function getAll(int $pagination): LengthAwarePaginator
    {
        return Order::where('payment_status', 'paid')->paginate($pagination);
    }

    public function getById(int $id): ?Model
    {
        return Order::where('payment_status', 'paid')->where('id', $id)->firstOrFail();
    }
}
