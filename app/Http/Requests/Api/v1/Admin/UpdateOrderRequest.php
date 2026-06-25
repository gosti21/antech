<?php

namespace App\Http\Requests\Api\v1\Admin;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'status' => [
                'required',
                'string',
                'in:processing,refunded',
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('id');
        $cover = Order::find($id);
        if (!$cover) {
            throw new NotFoundException();
        }
    }
}
