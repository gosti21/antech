<?php

namespace App\Http\Resources\Api\v1\Admin;

use App\Enums\Api\v1\Admin\MovementReasonType;
use App\Enums\Api\v1\Admin\MovementType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementShortResource extends JsonResource
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
            'type' => MovementType::from($this->type)->label(),
            'reason' => MovementReasonType::from($this->reason)->label(),
            'date' => $this->created_at->format('d-m-Y'),
            'time' => $this->created_at->format('H:i:s'),
        ];
    }
}
