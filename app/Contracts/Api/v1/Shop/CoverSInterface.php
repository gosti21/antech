<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Collection;

interface CoverSInterface
{
    public function getAll(): Collection;
}
