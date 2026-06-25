<?php

namespace App\Http\Requests\Api\v1\Admin\District;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\District;
use App\Rules\Api\v1\Admin\Address\MaxDeliveryDays;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDistrictRequest extends FormRequest
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
                'sometimes',
                'required',
                'between:3, 80',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/',
                Rule::unique('districts')->ignore($this->route('district'))
            ],
            'province_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:provinces,id'
            ],
            'delivery_price' => [
                'sometimes',
                'required',
                'numeric:strict',
                'decimal:2',
            ],
            'min_delivery_days' => [
                'sometimes',
                'required',
                'min:0',
                'integer:strict',
            ],
            'max_delivery_days' => [
                'sometimes',
                'required',
                'min:0',
                'integer:strict',
                new MaxDeliveryDays
            ],
            'status' => [
                'sometimes',
                'boolean:strict'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('district');
        if (!District::find($id)) {
            throw new NotFoundException();
        }
    }
}
