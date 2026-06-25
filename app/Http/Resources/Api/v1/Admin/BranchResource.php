<?php

namespace App\Http\Resources\Api\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'phone' => [
                'id' => $this->phone->id,
                'number' => $this->phone->number,
                'prefix' => '+' . $this->phone->prefix->prefix,
            ],
            'address' => [
                'id' => $this->address->id,
                'distric' => $this->address->district->name,
                'street' => $this->address->street . ' #' . $this->address->street_number,
            ]
        ];
    }
}
