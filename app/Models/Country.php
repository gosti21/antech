<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function departaments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
