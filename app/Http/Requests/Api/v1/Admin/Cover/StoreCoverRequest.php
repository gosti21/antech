<?php

namespace App\Http\Requests\Api\v1\Admin\Cover;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreCoverRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'between:3, 100',
                'unique:covers,title'

            ],
            'start_at' => [
                'required',
                Rule::date()->afterOrEqual(today())
            ],
            'end_at' => [
                'nullable',
                'date',
                'after_or_equal:start_at'
            ],
            'image' => [
                'required',
                File::image()
            ]
        ];
    }
}
