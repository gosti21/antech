<?php

namespace App\Rules\Api\v1\Admin\OptionProduct;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class OptionProductValuesBelongsToOption implements ValidationRule
{
    protected int $optionProductId;

    public function __construct(int $optionProductId)
    {
        $this->optionProductId = $optionProductId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $option_id = DB::table('option_product')
            ->where('id', $this->optionProductId)
            ->value('option_id');

        $exists = DB::table('option_values')
            ->where('id', $value)
            ->where('option_id', $option_id)
            ->exists();

        if (! $exists) {
            $fail("El valor seleccionado no pertenece a la opción especificada.");
        }
    }
}
