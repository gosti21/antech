<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\BranchVariantInterface as AdminBranchVariantInterface;
use App\Models\BranchVariant;
use Illuminate\Database\Eloquent\Collection;

class BranchVariantRepository implements AdminBranchVariantInterface
{
    public function getAllList(): Collection
    {
        return BranchVariant::with(['variant', 'variant.optionProductValues'])->get();
    }
}
