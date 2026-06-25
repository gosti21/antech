<?php

namespace App\Http\Resources\Api\v1\Admin;

use App\Enums\Api\v1\TypeSale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'type_voucher' => $this->voucher->type ?? 'boleta',
            'order_number' => $this->order_number,
            'type_sale' => TypeSale::from($this->type_sale)->label(),
            'total' => $this->total,
            'employee' => $this->employee->user-> name ?? 'Venta online',
            'customer' => $this->customer->name ?? $this->customer->business_name ?? 'Cliente no registrado',
            'path' => $this->voucher->path ?? '-'
        ];
    }
}
