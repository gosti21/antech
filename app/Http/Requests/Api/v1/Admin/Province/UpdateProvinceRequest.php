<?php

namespace App\Http\Requests\Api\v1\Admin\Province;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Province;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProvinceRequest extends FormRequest
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
                Rule::unique('provinces')->ignore($this->route('province'))
            ],
            'department_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:departments,id'
            ],
            'status' => [
                'sometimes',
                'boolean:strict'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('province');
        if (!Province::find($id)) {
            throw new NotFoundException();
        }
    }
}
