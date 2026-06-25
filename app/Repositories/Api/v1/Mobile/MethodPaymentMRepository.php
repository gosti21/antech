<?php

namespace App\Repositories\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\MethodPaymentMInterface;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;

class MethodPaymentMRepository implements MethodPaymentMInterface
{
    public function getYape(): ?Model
    {
        $payment = PaymentMethod::where('name', 'yape')
            ->first();
        return $payment;
    }

    public function getPlin(): ?Model
    {
        $payment = PaymentMethod::where('name', 'plin')
            ->first();
        return $payment;
    }
}
