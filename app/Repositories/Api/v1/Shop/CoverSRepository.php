<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\CoverSInterface;
use App\Models\Cover;
use Illuminate\Database\Eloquent\Collection;

class CoverSRepository implements CoverSInterface
{
    public function getAll(): Collection
    {
        return Cover::orderBy('order', 'asc')->where('status', true)->get();
    }
}
