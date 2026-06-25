<?php

namespace App\Http\Resources\Api\v1\Mobile;

use App\Enums\Api\v1\PaymentMethodType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderMResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'total' => $this->total,
            'date' => $this->created_at->format('d-m-Y'),
            'time' => $this->created_at->format('H:i:s'),
            'total_items' => $this->orderDetails->sum('quantity'),
            'type_voucher' => $this->voucher->type,
            'method_payment' => PaymentMethodType::from($this->paymentMethods->first()?->name)->label(),
            'voucher' => $this->voucher->path,
            'voucher_id' => $this->voucher->id,
        ];
    }
}
