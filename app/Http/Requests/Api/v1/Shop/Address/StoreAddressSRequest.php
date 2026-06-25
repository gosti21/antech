<?php

namespace App\Http\Requests\Api\v1\Shop\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressSRequest extends FormRequest
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
            'street' => [
                'required',
                'string',
                'between:3, 150',
            ],
            'street_number' => [
                'required',
                'integer:strict',
            ],
            'reference' => [
                'required',
                'between:3, 150',
            ],
            'district_id' => [
                'required',
                'integer:strict',
                'exists:districts,id'
            ],
        ];
    }
}
