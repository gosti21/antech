<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BranchVariant extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $fillable = [
        'stock',
        'stock_min',
        'branch_id',
        'variant_id'
    ];

    public function movements(): BelongsToMany
    {
        return $this->belongsToMany(Movement::class, 'inventory_movement', 'branch_variant_id','movement_id')->using(InventoryMovement::class)->withPivot('id', 'quantity')->withTimestamps();
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'cart_detail', 'branch_variant_id', 'cart_id')->using(CartDetail::class)->withPivot('id', 'quantity', 'unit_price')->withTimestamps();
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_detail', 'branch_variant_id', 'order_id')
            ->using(OrderDetail::class)->withPivot('id', 'product_name', 'variant_sku', 'unit_price', 'discount', 'quantity', 'subtotal')->withTimestamps();
    }
}
