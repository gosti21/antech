<?php

namespace App\Rules\Api\v1\Admin\Address;

use App\Models\District;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Route;

class MaxDeliveryDays implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $districId = Route::current()->parameter('distric');
        $distric = District::with('shippingRate')->find($districId);

        if (! $distric || ! $distric->shippingRate) {
            return;
        }

        $min_delivery_days = request()->input('min_delivery_days', $distric->shippingRate->min_delivery_days);

        if ($value < $min_delivery_days) {
            $fail('El día máximo debe ser igual o posterior al mínimo día de delivery.');
        }
    }
}
