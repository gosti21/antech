<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ShippingRate extends Model
{
    protected $fillable = [
        'delivery_price',
        'min_delivery_days',
        'max_delivery_days',
    ];

    public function shippable(): MorphTo
    {
        return $this->morphTo();
    }
}
