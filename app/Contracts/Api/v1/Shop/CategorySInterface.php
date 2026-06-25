<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CategorySInterface
{
    public function getAll(): Collection;
    public function getById(int $id): Model;
}
