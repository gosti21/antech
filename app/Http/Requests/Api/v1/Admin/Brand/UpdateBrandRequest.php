<?php

namespace App\Http\Requests\Api\v1\Admin\Brand;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Brand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
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
                'between:2, 60',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s-]+$/',
                Rule::unique('brands')->ignore($this->route('brand'))
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
        $id = $this->route('brand');
        if (!Brand::find($id)) {
            throw new NotFoundException();
        }
    }
}
