<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\AddressSInterface;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AddressSRepository implements AddressSInterface
{
    public function getAll(int $userId): Collection
    {
        return Address::where('addressable_type', User::class)->where('addressable_id', $userId)->get();
    }

    public function getById(int $id): Model
    {
        return Address::findOrFail($id);
    }

    public function favorite(int $userId): ?Model
    {
        return Address::where('addressable_type', User::class)->where('addressable_id', $userId)->where('favorite', true)->first();
    }

    public function create(array $data, Model $model): Model
    {
        return $model->addresses()->create($data)->refresh();
    }

    public function update(array $data, int $id): Model
    {
        $model = $this->getById($id);
        $model->update($data);
        return $model->refresh();
    }

    public function delete(int $id): bool
    {
        $model = $this->getById($id);
        return $model->delete();
    }

    public function getByPrice(int $id): float
    {
        $model = $this->getById($id);

        return $model->district?->shippingRate?->delivery_price ?? 0.0;
    }
}
