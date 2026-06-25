<?php

namespace App\Http\Requests\Api\v1\Admin\Option;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Option;
use App\Rules\Api\v1\Admin\Option\UniqueOptionValues;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOptionRequest extends FormRequest
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
                Rule::unique('options')->ignore($this->route('option'))
            ],
            'status' => [
                'sometimes',
                'required',
                'boolean:strict'
            ],
            'option_values' => [
                'sometimes',
                'required',
                'array',
                'min:1',
                new UniqueOptionValues
            ],
            'option_values.*.id' => [
                'nullable',
                'integer',
                Rule::exists('option_values', 'id'),
            ],
            'option_values.*.value' => [
                'sometimes',
                'required',
                'string',
                'between:1, 20'
            ],
            'option_values.*.description' => [
                'sometimes',
                'required',
                'string',
                'between:1, 60'
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('option');
        if (!Option::find($id)) {
            throw new NotFoundException();
        }
    }
}
