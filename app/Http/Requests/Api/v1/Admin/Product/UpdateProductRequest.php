<?php

namespace App\Http\Requests\Api\v1\Admin\Product;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Product;
use App\Rules\Api\v1\Admin\Product\UniqueSpecificationIds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
                'between:3, 100',
                Rule::unique('products')->ignore($this->route('product'))
            ],
            'model' => [
                'sometimes',
                'required',
                'string',
                'between:3, 80',
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
                'min:10'
            ],
            'status' => [
                'sometimes',
                'boolean:strict'
            ],
            'subcategory_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:subcategories,id'
            ],
            'brand_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:brands,id'
            ],
            'specifications' => [
                'sometimes',
                'required',
                'array',
                'min:1',
                new UniqueSpecificationIds,
            ],
            'specifications.*.specification_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:specifications,id'
            ],
            'specifications.*.value' => [
                'sometimes',
                'required',
                'string',
                'min:2'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('product');
        if (!Product::find($id)) {
            throw new NotFoundException();
        }
    }
}
