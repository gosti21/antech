<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Employee extends Model
{
    protected $fillable = [
        'status',
        'salary',
        'position',
        'branch_id',
        'user_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function phone(): MorphOne
    {
        return $this->morphOne(Phone::class, 'phoneable');
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
