<?php

namespace App\Http\Requests\Api\v1\Admin\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
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
                'string',
                'between:3, 100',
                'unique:branches,name',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'between:5, 150',
                'unique:branches,email',
            ],
            'prefix' => [
                'required',
                'integer:strict',
                'exists:prefixes,id'
            ],
            'number' => [
                //mejorar el tema del tamaño
                'required',
                'min:9',
                'integer:strict',
                'unique:phones,number',
            ]
        ];
    }
}
