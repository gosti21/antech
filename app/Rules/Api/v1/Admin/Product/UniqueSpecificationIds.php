<?php

namespace App\Rules\Api\v1\Admin\Product;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueSpecificationIds implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            return;
        }

        $ids = array_column($value, 'specification_id');

        if (count($ids) !== count(array_unique($ids))) {
            $fail('Cada especificación debe tener un specification_id único.');
        }
    }
}
