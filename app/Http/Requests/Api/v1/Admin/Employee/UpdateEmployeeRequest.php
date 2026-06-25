<?php

namespace App\Http\Requests\Api\v1\Admin\Employee;

use App\Enums\Api\v1\Admin\EmployeePosition;
use App\Enums\Api\v1\DocumentType;
use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateEmployeeRequest extends FormRequest
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
        $employee = Employee::find($this->route('employee'));

        return [
            'name' => [
                'sometimes',
                'required',
                'between:3, 50',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'last_name' => [
                'sometimes',
                'required',
                'between:3, 65',
                'regex:/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'email' => [
                'sometimes',
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')
                    ->ignore($employee->user_id),
            ],
            'password' => [
                'sometimes',
                'required',
                Password::min(8)
            ],
            'date_birth' => [
                'sometimes',
                'nullable',
                'date',
                'before:today'
            ],
            'salary' => [
                'sometimes',
                'required',
                'numeric:strict',
                'decimal:2',
                'min:0',
                'max:9999.99'
            ],
            'position' => [
                'sometimes',
                'required',
                Rule::enum(EmployeePosition::class)
            ],
            'phone' => [
                'sometimes',
                'required',
                'min:9',
                'integer:strict',
                Rule::unique('phones', 'number')
                    ->where(fn($q) => $q->where('phoneable_type', Employee::class))
                    ->ignore($this->route('employee'), 'phoneable_id'),
            ],
            'document_type' => [
                'sometimes',
                'required',
                Rule::enum(DocumentType::class)
            ],
            'document_number' => [
                'sometimes',
                'required',
                'integer:strict',
                'min_digits:8',
                'max_digits:15',
                Rule::unique('document_numbers', 'number')
                    ->where(fn($q) => $q->where('documentable_type', Employee::class))
                    ->ignore($this->route('employee'), 'documentable_id'),
            ],
            'status' => [
                'sometimes',
                'required',
                'boolean:strict'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('employee');
        if (!Employee::find($id)) {
            throw new NotFoundException();
        }

        if ($this->has('salary')) {
            $this->merge([
                'salary' => number_format((float) $this->input('salary'), 2, '.', ''),
            ]);
        }
    }
}
