<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Customer extends Model
{
    protected $fillable = [
        'type_customer',
        'name',
        'last_name',
        'business_name',
        'tax_address',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentNumber(): MorphOne
    {
        return $this->morphOne(DocumentNumber::class, 'documentable');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
