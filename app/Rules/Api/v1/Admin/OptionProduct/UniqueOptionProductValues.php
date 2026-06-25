<?php

namespace App\Rules\Api\v1\Admin\OptionProduct;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueOptionProductValues implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Verificamos que sea un array
        if (!is_array($value)) {
            $fail('El campo :attribute debe ser un array.');
            return;
        }

        // Extraemos los IDs de los valores
        $optionValueIds = array_column($value, 'option_value_id');

        // Buscamos duplicados
        $duplicates = array_unique(array_diff_assoc($optionValueIds, array_unique($optionValueIds)));

        if (!empty($duplicates)) {
            $fail('El campo :attribute contiene valores duplicados en option_value_id.');
        }
    }
}
