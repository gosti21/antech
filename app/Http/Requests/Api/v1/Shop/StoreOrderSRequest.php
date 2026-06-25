<?php

namespace App\Http\Requests\Api\v1\Shop;

use App\Enums\Api\v1\DeliveryType;
use App\Enums\Api\v1\DocumentType;
use App\Enums\Api\v1\VoucherType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderSRequest extends FormRequest
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
            'type_voucher' => [
                'required',
                'string',
                Rule::enum(VoucherType::class)
            ],
            'delivery_type' => [
                'required',
                'string',
                Rule::enum(DeliveryType::class)
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
            ],
            'customer' => [
                'required',
                'array',
            ],
            'customer.name' => [
                'string',
                'between:3, 60',
                'required_with:customer.last_name',
            ],
            'customer.last_name' => [
                'string',
                'between:3, 60',
                'required_with:customer.name',
            ],
            'customer.business_name' => [
                'string',
                'between:3, 80',
                'required_with:customer.tax_address',
            ],
            'customer.tax_address' => [
                'string',
                'between:1, 120',
                'required_with:customer.business_name',
            ],
            'receiver_info' => [
                'required',
                'array',
            ],
            'receiver_info.name' => [
                'required',
                'string',
                'between:3, 60',
            ],
            'receiver_info.last_name' => [
                'required',
                'string',
                'between:3, 60',
            ],
            'receiver_info.phone' => [
                'required',
                'digits:9',
            ],
            'receiver_info.document_type' => [
                'required',
                Rule::enum(DocumentType::class)
            ],
            'receiver_info.document_number' => [
                'required',
                'integer:strict',
                'min_digits:8',
                'max_digits:15',
            ],
            'address_id' => [
                'required',
                'integer:strict',
                'exists:addresses,id',
            ],
        ];
    }
}
