<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'model',
        'status',
        'subcategory_id',
        'brand_id'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function specifications(): BelongsToMany
    {
        return $this->belongsToMany(Specification::class)->withPivot('value')->withTimestamps();
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class)->using(OptionProduct::class)->withPivot('id')->withTimestamps();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }
}
