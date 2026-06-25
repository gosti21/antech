<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\CustomerSInterface;
use App\Models\Customer;
use App\Models\DocumentNumber;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Model;

class CustomerSRepository implements CustomerSInterface
{
    public function getBYCustomerDNI(string $dni): ?Model
    {
        $doc = DocumentNumber::where('number', $dni)
            ->whereRelation('documentType', 'type', 'DNI')
            ->where('documentable_type', Customer::class)
            ->first();
        return $doc?->documentable;
    }

    public function getBYCustomerRUC(string $ruc): ?Model
    {
        $doc = DocumentNumber::where('number', $ruc)
            ->whereRelation('documentType', 'type', 'RUC')
            ->where('documentable_type', Customer::class)
            ->first();
        return $doc?->documentable;
    }

    public function findByDocumentNumber(string $documentNumber): ?Model
    {
        return DocumentNumber::where('number', $documentNumber)
            ->where('documentable_type', Customer::class)
            ->first();
    }

    public function getDocumentType(string $documentType): ?Model
    {
        return DocumentType::where('type', $documentType)->firstOrFail();
    }

    public function create(array $customerData, string $documentType, string $documentNumber, int $userId): Model
    {
        $documentTypeId = $this->getDocumentType($documentType);

        $customer = Customer::create([
            'type_customer' => $documentType === 'RUC' ? 'company' : 'people',
            'name' => $customerData['name'] ?? null,
            'last_name' => $customerData['last_name'] ?? null,
            'business_name' => $customerData['business_name'] ?? null,
            'tax_address' => $customerData['tax_address'] ?? null,
            'user_id' => $userId
        ])->fresh();
        $customer->documentNumber()->create([
            'number' => $documentNumber,
            'document_type_id' => $documentTypeId->id
        ]);

        return $customer->fresh();
    }
}
