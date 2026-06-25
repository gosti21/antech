<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\BranchSInterface;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;

class BranchSRepository implements BranchSInterface
{
    public function getAll(): Collection
    {
        return Branch::with('address')->get();
    }
}
