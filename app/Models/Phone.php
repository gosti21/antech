<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Phone extends Model
{
    protected $fillable = [
        'number',
        'prefix_id',
        'phoneable_id',
        'phoneable_type'
    ];

    public function prefix(): BelongsTo
    {
        return $this->belongsTo(Prefix::class);
    }

    public function phoneable(): MorphTo
    {
        return $this->morphTo();
    }
}
