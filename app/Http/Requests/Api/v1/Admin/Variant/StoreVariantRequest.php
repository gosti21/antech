<?php

namespace App\Http\Requests\Api\v1\Admin\Variant;

use App\Rules\Api\v1\Admin\Shared\GreaterThanZeroInt;
use App\Rules\Api\v1\Admin\Variant\GreaterThanZero;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreVariantRequest extends FormRequest
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
            'selling_price' => [
                new GreaterThanZero,
                'required',
                'numeric:strict',
                'decimal:2',
            ],
            'purcharse_price' => [
                'required',
                'numeric:strict',
                'decimal:2',
                new GreaterThanZero
            ],
            'product_id' => [
                'required',
                'integer:strict',
                'exists:products,id'
            ],
            'stock_min' => [
                'required',
                'integer:strict',
                new GreaterThanZeroInt
            ],
            'images' => [
                'required',
                'array',
                'min:1'
            ],
            'images.*.image' => [
                'required',
                File::image()
            ],
            'features' => [
                'required',
                'array',
                'min:1'
            ],
            'features.*.option_product_value' => [
                'required',
                'integer:strict',
                'exists:option_product_value,id',
                'distinct',
            ],
        ];
    }

    protected function prepareForValidation()
    {
        if (isset($this->selling_price)) {
            $this->merge([
                'selling_price' => number_format($this->selling_price, 2, '.', '')
            ]);
        }

        if (isset($this->purcharse_price)) {
            $this->merge([
                'purcharse_price' => number_format($this->purcharse_price, 2, '.', '')
            ]);
        }
    }
}
