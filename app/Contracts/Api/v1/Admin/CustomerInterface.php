<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerInterface
{
    public function getAll(int $pagination): LengthAwarePaginator;
}
