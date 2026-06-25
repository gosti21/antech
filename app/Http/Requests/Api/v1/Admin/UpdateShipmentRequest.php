<?php

namespace App\Http\Requests\Api\v1\Admin;

use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Shipment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
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
                'sometimes',
                'required',
                'string',
                'in:ready_for_pickup,dispatched,in_transit,delivered,picked_up,failed,returned',
            ],
            'tracking_number' => [
                'sometimes',
                'required',
                'string',
                'between:2,15',
                'unique:shipments,tracking_number',
            ],
            'shipping_company_id' => [
                'sometimes',
                'required',
                'integer:strict',
                'exists:shipping_companies,id'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->route('id');
        $cover = Shipment::find($id);
        if (!$cover) {
            throw new NotFoundException();
        }
    }
}
