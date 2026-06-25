<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Model;

interface UserSInterface
{
    public function getById(int $id): Model;
}
