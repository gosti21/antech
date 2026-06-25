<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface SaleInterface
{
    public function getAll(int $pagination): LengthAwarePaginator;
    public function getById(int $id): ?Model;
}
