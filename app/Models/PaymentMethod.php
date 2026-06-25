<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'type',
        'required_qr',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
