<?php

namespace App\Http\Requests\Api\v1\Admin\Cover;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReorderCoverRequest extends FormRequest
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
            'sorts' => [
                'required',
                'array',
                'min:1'
            ],
            'sorts.*' => [
                'integer:strict',
                Rule::exists('covers', 'id')
            ],
        ];
    }
}
