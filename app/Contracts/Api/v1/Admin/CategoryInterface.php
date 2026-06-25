<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CategoryInterface extends BaseInterface
{
    public function create(array $data): Model;
    public function update(array $data, int $id): Model;
    public function getSubcategories(int $id): Collection;
}
