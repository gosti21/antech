<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OptionProductValue extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $fillable = [
        'option_value_id',
        'option_product_id',
    ];

    public function Variants(): BelongsToMany
    {
        return $this->belongsToMany(
            Variant::class,
            'feature_variant',
            'option_product_value_id',
            'variant_id'
        )->withTimestamps();;
    }

    public function optionValue(): BelongsTo
    {
        return $this->belongsTo(OptionValue::class, 'option_value_id');
    }
}
