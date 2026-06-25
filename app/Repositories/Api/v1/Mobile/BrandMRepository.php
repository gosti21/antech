<?php

namespace App\Repositories\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\BrandMInterface;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

class BrandMRepository implements BrandMInterface
{
    public function getAllList(): Collection
    {
        return Brand::orderBy('name', 'asc')->get();
    }
}
