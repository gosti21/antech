<?php

namespace App\Http\Requests\Api\v1\Admin\Country;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
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
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/',
                'unique:countries,name',
            ],
            'iso_code' => [
                'required',
                'between:1, 2',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/',
                'unique:countries,iso_code',
            ]
        ];
    }
}
