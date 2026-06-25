<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Model;

interface UserMInterface
{
    public function getById(int $id): Model;
}
