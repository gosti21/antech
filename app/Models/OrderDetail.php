<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderDetail extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $fillable = [
        'product_name',
        'variant_sku',
        'unit_price',
        'discount',
        'quantity',
        'subtotal',
        'order_id',
        'branch_variant_id',
    ];
}
