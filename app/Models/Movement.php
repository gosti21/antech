<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movement extends Model
{
    protected $fillable = [
        'type',
        'reason',
        'movement_number',
        'detail_transaction',
        'order_id',
    ];

    public function branchVariants(): BelongsToMany
    {
        return $this->belongsToMany(BranchVariant::class, 'inventory_movement', 'movement_id', 'branch_variant_id')->using(InventoryMovement::class)->withPivot('id', 'quantity')->withTimestamps();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
