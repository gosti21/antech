<?php

namespace App\Http\Requests\Api\v1\Admin\OptionValue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOptionValueRequest extends FormRequest
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
            'option_id' => [
                'required',
                'integer:strict',
                'exists:options,id'
            ],
            'value' => [
                'required',
                'string',
                'between:1, 20',
                Rule::unique('option_values')
                    ->where(fn($query) => $query->where('option_id', $this->option_id))
            ],
            'description' => [
                'required',
                'string',
                'between:2, 60'
            ]
        ];
    }
}
