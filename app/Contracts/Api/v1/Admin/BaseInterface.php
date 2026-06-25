<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseInterface {
    public function getAll(int $pagination): LengthAwarePaginator;
    public function getAllList(): Collection;
    public function getById(int $id): Model;
}
