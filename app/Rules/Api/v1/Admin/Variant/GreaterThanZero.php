<?php

namespace App\Rules\Api\v1\Admin\Variant;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GreaterThanZero implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value) || $value <= 0) {
            $fail("El campo $attribute debe ser mayor a 0.00");
        }
    }
}
