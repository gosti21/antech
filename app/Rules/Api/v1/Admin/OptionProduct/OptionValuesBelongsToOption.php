<?php

namespace App\Rules\Api\v1\Admin\OptionProduct;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class OptionValuesBelongsToOption implements ValidationRule
{
    protected int $optionId;

    public function __construct(int $optionId)
    {
        $this->optionId = $optionId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Verificar que option_value_id pertenezca al option_id correcto
        $exists = DB::table('option_values')
            ->where('id', $value)
            ->where('option_id', $this->optionId)
            ->exists();

        if (! $exists) {
            $fail("El valor seleccionado en {$attribute} no pertenece a la opción especificada.");
        }
    }
}
