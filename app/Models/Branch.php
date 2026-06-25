<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'email',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function phone(): MorphOne
    {
        return $this->morphOne(Phone::class, 'phoneable');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(Variant::class)->using(BranchVariant::class)->withPivot('stock', 'stock_min', 'id')->withTimestamps();
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
