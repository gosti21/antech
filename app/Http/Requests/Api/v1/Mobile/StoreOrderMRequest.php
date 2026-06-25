<?php

namespace App\Http\Requests\Api\v1\Mobile;

use App\Enums\Api\v1\DocumentType;
use App\Enums\Api\v1\PaymentMethodType;
use App\Enums\Api\v1\VoucherType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderMRequest extends FormRequest
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
            'payment_method' => [
                'required',
                'string',
                Rule::enum(PaymentMethodType::class)
            ],
            'payment_method_code' => [
                'sometimes',
                'string',
                'unique:order_payment_method,transaction_id',
            ],
        ];
    }
}
