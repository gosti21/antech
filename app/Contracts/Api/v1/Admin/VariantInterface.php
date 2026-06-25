<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface VariantInterface extends BaseInterface
{
    public function getAllShort(int $pagination, int $id): LengthAwarePaginator;
    public function create(array $variantData, array $images, array $variantFeatures, int $stockmin): Model;
    public function update(array $variantData, ?int $stockmin, ?array $images, ?array $variantFeatures, int $id): Model;
}
