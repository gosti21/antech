<?php

namespace App\Http\Requests\Api\v1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GenerateVariantBarcodeRequest extends FormRequest
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
            'items' => [
                'required',
                'array',
                'min:1'
            ],
            'items.*.variant_id' => [
                'required',
                'integer:strict',
                'exists:variants,id',
                'distinct',
            ],
            'items.*.quantity' => [
                'required',
                'integer:strict',
                'min:1',
                'max:100',
            ],
        ];
    }
}
