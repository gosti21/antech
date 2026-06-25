<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\CustomerMInterface;
use App\Services\Api\v1\integrations\SearchDNIService;
use App\Services\Api\v1\integrations\SearchRUCService;

class CustomerMService
{
    public function __construct(
        protected CustomerMInterface $repository,
        protected SearchDNIService $serviceDNI,
        protected SearchRUCService $serviceRUC,
    ) {}

    public function getBYCustomerDNI(string $dni)
    {
        $customer = $this->repository->getBYCustomerDNI($dni);
        if ($customer) {
            return [
                'status'  => true,
                'message' => 'Cliente encontrado en base de datos',
                'data'    => [
                    'name'            => $customer->name,
                    'last_name'       => $customer->last_name,
                    'document_number' => $dni,
                ],
            ];
        }

        $reniecData = $this->serviceDNI->searchDNI($dni);

        if ($reniecData) {
            return [
                'status'   => true,
                'message' => 'Datos obtenidos desde RENIEC',
                'data'    => $reniecData,
            ];
        }

        return [
            'status'   => false,
            'message' => 'DNI no encontrado',
        ];
    }

    public function getBYCustomerRUC(string $ruc)
    {
        $customer = $this->repository->getBYCustomerRUC($ruc);
        if ($customer) {
            return [
                'status'  => true,
                'message' => 'Cliente encontrado en base de datos',
                'data'    => [
                    'business_name' => $customer->business_name,
                    'tax_address' => $customer->tax_address,
                    'document_number' => $ruc,
                ],
            ];
        }

        $sunatData = $this->serviceRUC->searchRUC($ruc);

        if ($sunatData) {
            return [
                'status'   => true,
                'message' => 'Datos obtenidos desde SUNAT',
                'data'    => $sunatData,
            ];
        }

        return [
            'status'   => false,
            'message' => 'RUC no encontrado',
        ];
    }

    public function getOrCreate(string $documentNumber, string $documentType, array $customerData, int $userId)
    {
        $exists = $this->repository->findByDocumentNumber($documentNumber);
        if(!$exists){
            $customer = $this->repository->create($customerData, $documentType, $documentNumber, $userId);
            return $customer->id;
        }

        return $exists->documentable->id;
    }
}
