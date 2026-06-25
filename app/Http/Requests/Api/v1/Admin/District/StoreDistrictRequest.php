<?php

namespace App\Http\Requests\Api\v1\Admin\District;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistrictRequest extends FormRequest
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
                'unique:districts,name',
            ],
            'province_id' => [
                'integer:strict',
                'required',
                'exists:provinces,id'
            ],
            'delivery_price' => [
                'required',
                'numeric:strict',
                'decimal:2',
            ],
            'min_delivery_days' => [
                'required',
                'min:0',
                'integer:strict',
            ],
            'max_delivery_days' => [
                'required',
                'min:0',
                'integer:strict',
                'gte:min_delivery_days',
            ],
        ];
    }
}
