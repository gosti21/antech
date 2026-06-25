<?php

namespace App\Http\Requests\Api\v1\Admin\Specification;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecificationRequest extends FormRequest
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
                'between:3, 100',
                'unique:specifications,name',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/'
            ]
        ];
    }
}
