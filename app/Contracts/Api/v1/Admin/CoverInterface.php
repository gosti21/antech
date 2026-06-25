<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Model;

interface CoverInterface extends BaseInterface
{
    public function create(array $data): Model;
    public function update(array $coverData, ?string $imagePath, int $id): Model;
    public function reorder(array $orderIds): void;
}
