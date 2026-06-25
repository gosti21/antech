<?php

namespace App\Rules\Api\v1\Admin\Branch;

use App\Models\Branch;
use App\Models\Phone;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhoneNumberForBranch implements ValidationRule
{
    protected $branchId;

    public function __construct($branchId)
    {
        $this->branchId = $branchId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $branch = Branch::with('phone')->find($this->branchId);

        if ($branch && $branch->phone && $branch->phone->number == $value) {
            return;
        }

        $exists = Phone::where('number', $value)->exists();

        if ($exists) {
            $fail('El número ya está registrado.');
        }
    }
}
