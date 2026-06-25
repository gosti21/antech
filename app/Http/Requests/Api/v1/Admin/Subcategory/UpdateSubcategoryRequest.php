<?php

namespace App\Http\Requests\Api\v1\Admin\Subcategory;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Subcategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubcategoryRequest extends FormRequest
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
                'between:3,80',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/',
                Rule::unique('subcategories')
                    ->where('category_id', $this->input('category_id'))
                    ->ignore($this->route('subcategory'))
            ],
            'category_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:categories,id'
            ],
            'status' => [
                'sometimes',
                'boolean:strict'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('subcategory');
        if (!Subcategory::find($id)) {
            throw new NotFoundException();
        }
    }
}
