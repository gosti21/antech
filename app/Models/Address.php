<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'favorite',
        'street',
        'street_number',
        'reference',
        'district_id',
        'addressable_id',
        'addressable_type',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
