<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OptionProduct extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $fillable = [
        'product_id',
        'option_id'
    ];

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            OptionValue::class,
            'option_product_value',  // tabla pivot
            'option_product_id',     // FK local
            'option_value_id'        // FK del modelo relacionado
        )->using(OptionProductValue::class)->withPivot('id')->withTimestamps();
    }
}
