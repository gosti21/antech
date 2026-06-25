<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Model;

interface VoucherMInterface
{
    public function getById(int $id): Model;
}
