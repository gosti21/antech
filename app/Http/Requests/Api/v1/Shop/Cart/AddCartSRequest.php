<?php

namespace App\Http\Requests\Api\v1\Shop\Cart;

use App\Rules\Api\v1\Admin\Shared\GreaterThanZeroInt;
use Illuminate\Foundation\Http\FormRequest;

class AddCartSRequest extends FormRequest
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
            'branch_variant_id' => [
                'required',
                'integer:strict',
                'exists:branch_variant,id',
            ],
            'quantity' => [
                'required',
                'integer:strict',
                new GreaterThanZeroInt
            ]
        ];
    }
}
