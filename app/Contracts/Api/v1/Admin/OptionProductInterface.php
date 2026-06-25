<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface OptionProductInterface
{
    public function getById(int $id): Model;
    public function create(array $data): Model;
    public function addValues(array $data): Model;
    public function getAllValues(int $productId, int $optionId): Collection;
}
