<?php

namespace App\Services\Api\v1\integrations;

use App\Models\Order;

class ElectronicInvoiceService
{
    public function __construct(
        protected NubeFactService $nubeFactService,
        protected SunatApiService $sunatApiService,
        protected GreenterSunatService $greenterSunatService,
    ) {}

    public function generateVoucher(Order $order, array $customerData, string $voucherType): array
    {
        $provider = strtolower((string) config('integrations.billing.provider', 'nubefact'));

        if ($provider === 'sunat') {
            return $this->sunatApiService->generateVoucher($order, $customerData, $voucherType);
        }

        if ($provider === 'greenter') {
            return $this->greenterSunatService->generateVoucher($order, $customerData, $voucherType);
        }

        return $this->nubeFactService->generateVoucher($order, $customerData, $voucherType);
    }
}
