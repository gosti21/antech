<?php

namespace App\Http\Requests\Api\v1\Admin\Option;

use App\Enums\Api\v1\Admin\OptionType;
use App\Rules\Api\v1\Admin\Option\UniqueOptionValues;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOptionRequest extends FormRequest
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
                'unique:options,name',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'type' => [
                'required',
                Rule::enum(OptionType::class)
            ],
            'option_values' => [
                'required',
                'array',
                'min:1',
                new UniqueOptionValues
            ],
            'option_values.*.value' => [
                'required',
                'string',
                'between:1, 20'
            ],
            'option_values.*.description' => [
                'required',
                'string',
                'between:1, 60'
            ],
        ];
    }
}
