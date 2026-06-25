<?php

namespace App\Http\Requests\Api\v1\Admin\Specification;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Specification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecificationRequest extends FormRequest
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
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/',
                Rule::unique('specifications')->ignore($this->route('specification'))
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
        $id = $this->route('specification');
        if (!Specification::find($id)) {
            throw new NotFoundException();
        }
    }
}
