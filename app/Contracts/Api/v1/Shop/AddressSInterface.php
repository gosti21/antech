<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AddressSInterface
{
    public function getAll(int $userId): Collection;
    public function getById(int $id): Model;
    public function favorite(int $userId): ?Model;
    public function create(array $data, Model $model): Model;
    public function update(array $data, int $id): Model;
    public function delete(int $id): bool;
    public function getByPrice(int $id): float;
}
