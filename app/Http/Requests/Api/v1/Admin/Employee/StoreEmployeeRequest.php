<?php

namespace App\Http\Requests\Api\v1\Admin\Employee;

use App\Enums\Api\v1\Admin\EmployeePosition;
use App\Enums\Api\v1\DocumentType;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreEmployeeRequest extends FormRequest
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
                'between:3, 50',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'last_name' => [
                'required',
                'between:3, 65',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email'
            ],
            'password' => [
                'required',
                Password::min(8)
            ],
            'date_birth' => [
                'nullable',
                'date',
                'before:today'
            ],
            'salary' => [
                'required',
                'numeric:strict',
                'decimal:2',
                'min:0',
                'max:9999.99'
            ],
            'position' => [
                'required',
                Rule::enum(EmployeePosition::class)
            ],
            'phone' => [
                'required',
                'min:9',
                'integer:strict',
                'unique:phones,number',
            ],
            'document_type' => [
                'required',
                Rule::enum(DocumentType::class)
            ],
            'document_number' => [
                'required',
                'integer:strict',
                'min_digits:8',
                'max_digits:15',
                Rule::unique('document_numbers', 'number')
                    ->where(fn($q) => $q->where('documentable_type', Employee::class)),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('salary')) {
            $this->merge([
                'salary' => number_format((float) $this->input('salary'), 2, '.', ''),
            ]);
        }
    }
}
