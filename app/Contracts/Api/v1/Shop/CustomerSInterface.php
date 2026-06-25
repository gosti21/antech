<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Model;

interface CustomerSInterface {
    public function getBYCustomerDNI(string $dni): ?Model;
    public function getBYCustomerRUC(string $ruc): ?Model;
    public function findByDocumentNumber(string $documentNumber): ?Model;
    public function getDocumentType(string $documentType): ?Model;
    public function create(array $customerData, string $documentType, string $documentNumber, int $userId): Model;
}

