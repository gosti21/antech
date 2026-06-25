<?php

namespace App\Http\Requests\Api\v1\Admin\OptionProduct;

use App\Rules\Api\v1\Admin\OptionProduct\OptionValuesBelongsToOption;
use App\Rules\Api\v1\Admin\OptionProduct\UniqueOptionProductValues;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOptionProductRequest extends FormRequest
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
            'product_id' => [
                'required',
                'integer:strict',
                'exists:products,id'
            ],
            'option_id' => [
                'required',
                'integer:strict',
                'exists:options,id',
                Rule::unique('option_product')
                    ->where(fn($query) => $query->where('product_id', $this->input('product_id'))),
            ],
            'values' => [
                'required',
                'array',
                'min:1',
                new UniqueOptionProductValues,
            ],
            'values.*.option_value_id' => [
                'required',
                'integer:strict',
                'exists:option_values,id',
                new OptionValuesBelongsToOption($this->input('option_id')),
            ]
        ];
    }
}
