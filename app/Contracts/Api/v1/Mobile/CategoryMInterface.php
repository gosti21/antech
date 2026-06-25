<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Collection;

interface CategoryMInterface {
    public function getAllList(): Collection;
    public function getSubcategories(int $id): Collection;
}
