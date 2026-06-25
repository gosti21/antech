<?php

namespace App\Repositories\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\VariantMInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class VariantMRepository implements VariantMInterface
{
    public function getVariantSku(string $sku): Model
    {
        return Product::query()
            ->whereHas('variants', fn($q) => $q->where('sku', $sku))
            ->with([
                'brand',
                'specifications',
                'variants' => function ($q) {
                    $q->with([
                        'images',
                        'optionProductValues.optionValue',
                        'branches' => fn($b) => $b->where('branch_id', 1),
                    ]);
                }
            ])
            ->firstOrFail();
    }
}
