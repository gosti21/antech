<?php

namespace App\Http\Resources\Api\v1\Admin;

use App\Enums\Api\v1\Admin\EmployeeRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeShortResource extends JsonResource
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
            'user' => $this->whenLoaded('user', function () {
                return [
                    'name'      => $this->user->name,
                    'last_name' => $this->user->last_name,
                ];
            }),
            'phone' => $this->whenLoaded('phone', function () {
                return [
                    'number' => $this->phone->number,
                ];
            }),
            'rol' => EmployeeRole::from($this->user->roles->first()->name)->label(),
            'status' => $this->status,
        ];
    }
}
