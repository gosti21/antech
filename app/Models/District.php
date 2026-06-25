<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class District extends Model
{
    protected $fillable = [
        'name',
        'status',
        'province_id'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];


    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function shippingRate(): MorphOne
    {
        return $this->morphOne(ShippingRate::class, 'shippable');
    }
}
