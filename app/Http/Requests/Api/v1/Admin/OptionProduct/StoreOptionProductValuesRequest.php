<?php

namespace App\Http\Requests\Api\v1\Admin\OptionProduct;

use App\Rules\Api\v1\Admin\OptionProduct\OptionProductValuesBelongsToOption;
use App\Rules\Api\v1\Admin\OptionProduct\UniqueOptionProductValues;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOptionProductValuesRequest extends FormRequest
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
            'option_product_id' => [
                'required',
                'integer:strict',
                'exists:option_product,id'
            ],
            'option_value_id' => [
                'required',
                'integer:strict',
                'exists:option_values,id',
                Rule::unique('option_product_value', 'option_value_id')
                    ->where('option_product_id', $this->input('option_product_id')),
                new OptionProductValuesBelongsToOption($this->input('option_product_id')),
            ],
        ];
    }
}
