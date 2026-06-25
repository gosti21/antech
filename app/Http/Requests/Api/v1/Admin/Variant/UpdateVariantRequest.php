<?php

namespace App\Http\Requests\Api\v1\Admin\Variant;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Variant;
use App\Rules\Api\v1\Admin\Variant\GreaterThanZero;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateVariantRequest extends FormRequest
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
                'sometimes',
                'required',
                'numeric:strict',
                'decimal:2',
                new GreaterThanZero,
            ],
            'purcharse_price' => [
                'sometimes',
                'required',
                'numeric:strict',
                'decimal:2',
                new GreaterThanZero
            ],
            'product_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:products,id',
            ],
            'stock_min' => [
                'sometimes',
                'required',
                'integer:strict',
            ],
            'status' => [
                'sometimes',
                'required',
                'boolean:strict'
            ],
            'images' => [
                'sometimes',
                'required',
                'array',
                'min:1'
            ],
/*             'images.*.id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:images,id',
            ], */
            'images.*.image' => [
                'sometimes',
                'required',
                File::image()
            ],
            'features' => [
                'sometimes',
                'required',
                'array',
                'min:1'
            ],
            'features.*.option_product_value' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:option_product_value,id',
                'distinct',
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $id = $this->route('variant');
        if (!Variant::find($id)) {
            throw new NotFoundException();
        }

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
