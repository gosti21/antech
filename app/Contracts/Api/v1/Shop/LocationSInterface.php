<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Collection;

interface LocationSInterface
{
    public function getAllDepartments(): Collection;
    public function getProvinces(int $departmentId): Collection;
    public function getDistricts(int $provinceId): Collection;
}
