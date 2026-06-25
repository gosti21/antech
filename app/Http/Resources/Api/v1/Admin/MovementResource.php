<?php

namespace App\Http\Resources\Api\v1\Admin;

use App\Enums\Api\v1\Admin\MovementReasonType;
use App\Enums\Api\v1\Admin\MovementType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementResource extends JsonResource
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
            'detail_transaction' => $this->detail_transaction,
            'variants' => $this->whenLoaded('branchVariants', function () {
                return $this->branchVariants->map(fn($variant) => [
                    'id' => $variant->id,                  // branch_variant.id
                    'name' => $variant->variant->product->name,  // variant_id
                    'model' => $variant->variant->product->model,
                    'features' => $variant->variant->optionProductValues->map(
                        fn($feature) => [
                            'id' => $feature->option_value_id,
                            'description' => $feature->optionValue->description,
                        ]
                    ),
                    'quantity' => $variant->pivot->quantity,
                ]);
            }),
            'date' => $this->created_at->format('d-m-Y'),
            'time' => $this->created_at->format('H:i:s'),
        ];
    }
}
