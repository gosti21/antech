<?php

namespace App\Rules\Api\v1\Admin\Option;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueOptionValues implements ValidationRule
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

        $values = array_map(
            fn($item) => isset($item['value']) ? strtolower(trim($item['value'])) : null,
            $value
        );

        // Eliminamos nulls (por si algún elemento no tiene 'value')
        $values = array_filter($values);

        // Verificamos si hay duplicados
        if (count($values) !== count(array_unique($values))) {
            $fail('Los valores dentro de option_values no pueden repetirse.');
        }
    }
}
