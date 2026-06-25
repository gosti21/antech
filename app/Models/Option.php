<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function optionValues(): HasMany
    {
        return $this->hasMany(OptionValue::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->using(OptionProduct::class);
    }
}
