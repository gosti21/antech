<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Cover extends Model
{
    protected $fillable = [
        'title',
        'start_at',
        'end_at',
        'status',
        'order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
