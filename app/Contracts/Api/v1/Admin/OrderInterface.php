<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface OrderInterface
{
    public function getAll(int $pagination): LengthAwarePaginator;
    public function getById(int $id): Model;
    public function update(array $data, int $id): Model;
}
