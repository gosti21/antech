<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Collection;

interface BrandMInterface {
    public function getAllList(): Collection;
}
