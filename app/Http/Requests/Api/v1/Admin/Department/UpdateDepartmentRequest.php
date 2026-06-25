<?php

namespace App\Http\Requests\Api\v1\Admin\Department;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
                'between:3, 80',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/',
                Rule::unique('departments')->ignore($this->route('department'))
            ],
            'country_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:countries,id'
            ],
            'status' => [
                'sometimes',
                'boolean:strict'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('department');
        if (!Department::find($id)) {
            throw new NotFoundException();
        }
    }
}
