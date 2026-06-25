<?php

namespace App\Http\Resources\Api\v1\Admin;

use App\Enums\Api\v1\Admin\TypeCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $typeCustomer = request('type_customer');
        if($typeCustomer == 'company'){
            return [
                'id' => $this->id,
                'type_customer' => TypeCustomer::from($this->type_customer)->label(),
                'type_customer_en' => $this->type_customer,
                'business_name' => $this->business_name,
                'tax_address' => $this->tax_address,
                'type_document' => $this->documentNumber->documentType->type,
                'document_number' => $this->documentNumber->number,
            ];
        }
        return [
            'id' => $this->id,
            'type_customer' => TypeCustomer::from($this->type_customer)->label(),
            'type_customer_en' => $this->type_customer,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'type_document' => $this->documentNumber->documentType->type,
            'document_number' => $this->documentNumber->number,
        ];
    }
}
