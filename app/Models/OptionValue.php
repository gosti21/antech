<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OptionValue extends Model
{
    protected $fillable = [
        'value',
        'description',
        'option_id',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }

    public function optionProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            OptionProduct::class,
            'option_product_value',
            'option_value_id',
            'option_product_id',
        )->using(OptionProductValue::class)->withPivot('id')->withTimestamps();
    }
}
