<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\LocationSInterface;
use App\Models\Department;
use App\Models\Province;
use Illuminate\Database\Eloquent\Collection;

class LocationSRepository implements LocationSInterface
{
    public function getAllDepartments(): Collection
    {
        return Department::get(['id', 'name']);
    }

    public function getProvinces(int $departmentId): Collection
    {
        $department = Department::findOrFail($departmentId);
        return $department->provinces()->get(['id', 'name']);
    }

    public function getDistricts(int $provinceId): Collection
    {
        $province = Province::findOrFail($provinceId);
        return $province->districts()->get(['id', 'name']);
    }
}
