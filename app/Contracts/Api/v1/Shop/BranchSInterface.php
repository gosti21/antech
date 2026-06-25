<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Collection;

interface BranchSInterface
{
    public function getAll(): Collection;
}
