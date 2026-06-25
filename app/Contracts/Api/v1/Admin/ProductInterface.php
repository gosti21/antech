<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ProductInterface extends BaseInterface
{
    public function create(array $data): Model;
    public function update(array $productData, array $specificationsData, int $id): Model;
    public function getAllOptions(int $id): Model;
    public function getAllOptionsShort(int $id): Collection;
    public function hasOptions(int $id): bool;
}
