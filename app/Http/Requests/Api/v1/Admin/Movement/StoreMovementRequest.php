<?php

namespace App\Http\Requests\Api\v1\Admin\Movement;

use App\Enums\Api\v1\Admin\MovementReasonType;
use App\Enums\Api\v1\Admin\MovementType;
use App\Rules\Api\v1\Admin\Shared\GreaterThanZeroInt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::enum(MovementType::class)
            ],
            'reason' => [
                'required',
                Rule::enum(MovementReasonType::class)
            ],
            'detail_transaction' => [
                'nullable',
                'string',
                'between:3, 180',
            ],
            'variants' => [
                'required',
                'array',
                'min:1',
            ],
            'variants.*.branch_variant_id' => [
                'required',
                'integer:strict',
                'exists:branch_variant,id',
                'distinct'
            ],
            'variants.*.quantity' => [
                'required',
                'integer:strict',
                new GreaterThanZeroInt
            ],
        ];
    }
}
