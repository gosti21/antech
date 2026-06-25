<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'status',
        'expires_at',
        'user_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branchVariants(): BelongsToMany
    {
        return $this->belongsToMany(BranchVariant::class, 'cart_detail', 'cart_id', 'branch_variant_id')->using(CartDetail::class)->withPivot('id', 'quantity', 'unit_price')->withTimestamps();
    }

    //auxiliar
    public function auxBranchVariants(): HasMany
    {
        return $this->hasMany(CartDetail::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }


    public function calculateTotals(): array
    {
        $variants = $this->relationLoaded('branchVariants')
            ? $this->branchVariants
            : $this->branchVariants()->get();

        $total = $variants->sum(function ($variant) {
            return $variant->pivot->unit_price * $variant->pivot->quantity;
        });

        return [
            'total'       => round($total, 2),
            'items_count' => $variants->sum(fn($variant) => $variant->pivot->quantity),
        ];
    }
}
