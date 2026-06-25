<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Collection;

interface BranchVariantInterface
{
    public function getAllList(): Collection;
}
