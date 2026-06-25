<?php

namespace App\Rules\Api\v1\Admin\Cover;

use App\Models\Cover;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Route;

class EndAtDateCover implements ValidationRule
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

        $coverId = Route::current()->parameter('cover');
        $cover = Cover::find($coverId);

        $startAt = request()->input('start_at', $cover->start_at);

        if (strtotime($value) < strtotime($startAt)) {
            $fail('La fecha de fin debe ser igual o posterior a la fecha de inicio.');
        }
    }
}
