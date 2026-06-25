<?php

namespace App\Http\Resources\Api\v1\Admin;

use App\Enums\Api\v1\Admin\EmployeePosition;
use App\Enums\Api\v1\Admin\EmployeeRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'salary' => $this->salary,
            'position' => $this->position,
            'posicion' => EmployeePosition::from($this->position)->label(),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'name'      => $this->user->name,
                    'last_name' => $this->user->last_name,
                    'email'     => $this->user->email,
                ];
            }),
            'phone' => $this->whenLoaded('phone', function () {
                return [
                    'number' => $this->phone->number,
                    'prefix' => $this->phone->prefix->prefix,
                ];
            }),
            'document' => $this->whenLoaded('documentNumber', function() {
                return [
                    'number' => $this->documentNumber->number,
                    'type'   => $this->documentNumber->documentType->type,
                ];
            }),
            'rol' => EmployeeRole::from($this->user->roles->first()->name)->label(),
            'status' => $this->status,
        ];
    }
}
