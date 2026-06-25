<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Variant extends Model
{
    protected $fillable = [
        'sku',
        'selling_price',
        'purcharse_price',
        'status',
        'product_id'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)->using(BranchVariant::class)->withPivot('stock', 'stock_min', 'id')->withTimestamps();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function optionProductValues(): BelongsToMany
    {
        return $this->belongsToMany(
            OptionProductValue::class,
            'feature_variant',
            'variant_id',
            'option_product_value_id',
        )->withTimestamps();
    }

    #helper
    public function getFullNameAttribute(): string
    {
        $productName = $this->product->name;

        $options = $this->optionProductValues->map(function ($opv) {
            $optionName = $opv->optionValue->option->name;
            $optionDescription = $opv->optionValue->description;
            return "{$optionName}: {$optionDescription}";
        })->implode(' | ');

        return $options ? "{$productName} - {$options}" : $productName;
    }
}
