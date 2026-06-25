<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Model;

interface VariantMInterface
{
    public function getVariantSku(string $sku): Model;
}
