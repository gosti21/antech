<?php

namespace App\Http\Requests\Api\v1\Admin\Subcategory;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubcategoryRequest extends FormRequest
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
                Rule::unique('subcategories', 'name')
                ->where(fn(Builder $query) => $query->where('category_id', $this->category_id))
                //modificar el mensaje que devuelve
            ],
            'category_id' => [
                'integer:strict',
                'required',
                'exists:categories,id'
            ]
        ];
    }
}
