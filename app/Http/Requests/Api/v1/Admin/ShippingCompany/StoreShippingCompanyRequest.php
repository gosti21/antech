<?php

namespace App\Http\Requests\Api\v1\Admin\ShippingCompany;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingCompanyRequest extends FormRequest
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
            'name' => [
                'required',
                'between:3, 80',
                'unique:shipping_companies,name',
            ],
            'phone' => [
                'required',
                'digits:9',
            ],
            'email' => [
                'nullable'
            ],
            'district' => [
                'required',
                'string',
                'between:3, 60',
            ],
            'street' => [
                'required',
                'string',
                'between:3, 100',
            ],
            'reference' => [
                'required',
                'string',
                'between:3, 120',
            ],
        ];
    }
}
