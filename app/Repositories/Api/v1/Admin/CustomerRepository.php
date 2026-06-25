<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\CustomerInterface;
use App\Filters\Api\v1\Admin\Customer\TypeCustomerFilter;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class CustomerRepository implements CustomerInterface
{
    public function getAll(int $pagination): LengthAwarePaginator
    {
        $query = Customer::query()
            ->orderBy('id', 'asc');

        $filers = [
            TypeCustomerFilter::class
        ];

        $query = app(Pipeline::class)
            ->send($query)
            ->through($filers)
            ->thenReturn();

        return $query->paginate($pagination);
    }
}
