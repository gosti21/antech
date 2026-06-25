<?php

namespace App\Http\Requests\Api\v1\Admin\Branch;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Branch;
use App\Rules\Api\v1\Admin\Branch\UniquePhoneNumberForBranch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends FormRequest
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
                'string',
                'between:3, 100',
                Rule::unique('branches')->ignore($this->route('branch'))
            ],
            'email' => [
                'sometimes',
                'required',
                'email:rfc,dns',
                'between:5, 150',
                Rule::unique('branches')->ignore($this->route('branch'))
            ],
            'prefix' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:prefixes,id'
            ],
            'number' => [
                'sometimes',
                //mejorar el tema del tamaño
                'required',
                'min:9',
                'max_digits:10',
                'integer:strict',
                new UniquePhoneNumberForBranch($this->route('branch')),
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
        $id = $this->route('branch');
        if (!Branch::find($id)) {
            throw new NotFoundException();
        }
    }
}
