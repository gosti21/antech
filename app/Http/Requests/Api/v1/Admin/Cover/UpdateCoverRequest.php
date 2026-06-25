<?php

namespace App\Http\Requests\Api\v1\Admin\Cover;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Cover;
use App\Rules\Api\v1\Admin\Cover\EndAtDateCover;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateCoverRequest extends FormRequest
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
                'sometimes',
                'required',
                'string',
                'between:3, 100',
                Rule::unique('covers')->ignore($this->route('cover'))
            ],
            'start_at' => [
                'sometimes',
                'required',
            ],
            'end_at' => [
                'sometimes',
                'nullable',
                new EndAtDateCover
            ],
            'image' => [
                'sometimes',
                'required',
                File::image()
            ],
            'status' => [
                'sometimes',
                'boolean:strict'
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('cover');
        $cover = Cover::find($id);
        if (!$cover) {
            throw new NotFoundException();
        }
    }
}
