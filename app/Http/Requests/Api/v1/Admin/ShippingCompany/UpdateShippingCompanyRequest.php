<?php

namespace App\Http\Requests\Api\v1\Admin\ShippingCompany;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\ShippingCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShippingCompanyRequest extends FormRequest
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
                Rule::unique('shipping_companies')->ignore($this->route('courier')),
            ],
            'phone' => [
                'sometimes',
                'required',
                'digits:9',
            ],
            'email' => [
                'nullable'
            ],
            'district' => [
                'sometimes',
                'required',
                'string',
                'between:3, 60',
            ],
            'street' => [
                'sometimes',
                'required',
                'string',
                'between:3, 100',
            ],
            'reference' => [
                'sometimes',
                'required',
                'string',
                'between:3, 120',
            ],
            'status' => [
                'sometimes',
                'required',
                'boolean:strict'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('courier');
        if (!ShippingCompany::find($id)) {
            throw new NotFoundException();
        }
    }
}
