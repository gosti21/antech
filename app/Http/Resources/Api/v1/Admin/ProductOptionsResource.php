<?php

namespace App\Http\Resources\Api\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->whenLoaded('options', function () {
            return $this->options->map(function ($option) {
                return [
                    'id' => $option->pivot->id,
                    'product_id' => $option->pivot->product_id,
                    'option_id' => $option->pivot->option_id,
                    'option_name' => $option->name,
                    'option_type' => $option->type,
                    'values' => $option->pivot->optionValues->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'value' => $value->value,
                            'description' => $value->description
                        ];
                    }),
                ];
            })->toArray();
        }, []);
    }
}
