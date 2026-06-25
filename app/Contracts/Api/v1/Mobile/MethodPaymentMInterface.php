<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Model;

interface MethodPaymentMInterface
{
    public function getYape(): ?Model;
    public function getPlin(): ?Model;
}
