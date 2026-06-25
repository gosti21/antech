<?php

namespace App\Repositories\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\ProductMInterface;
use App\Filters\Api\v1\Mobile\Products\ProductBrandMFilter;
use App\Filters\Api\v1\Mobile\Products\ProductCategoryMFilter;
use App\Filters\Api\v1\Mobile\Products\ProductOrderByMFilter;
use App\Filters\Api\v1\Mobile\Products\ProductPriceMFilter;
use App\Filters\Api\v1\Mobile\Products\ProductSearchMFilter;
use App\Filters\Api\v1\Mobile\Products\ProductSubcategoryMFilter;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;

class ProductMRepository implements ProductMInterface
{
    private int $branchId = 1;

    public function getAll(int $pagination): LengthAwarePaginator
    {
        $query = Product::query()
            ->whereHas('variants.branches', function ($q) {
                $q->where('branch_id', $this->branchId);
            })
            ->with([
                'brand',
                'variants' => function ($q) {
                    $q->whereHas('branches', function ($b) {
                        $b->where('branch_id', $this->branchId);
                    })
                        ->with([
                            'images' => function ($img) {
                                $img->orderBy('id')->limit(1);
                            },
                            'branches' => function ($b) {
                                $b->where('branch_id', $this->branchId);
                            }
                        ])
                        ->orderBy('selling_price', 'asc')
                        ->limit(1);
                }
            ]);

        $filters = [
            ProductSearchMFilter::class,
            ProductBrandMFilter::class,
            ProductCategoryMFilter::class,
            ProductSubcategoryMFilter::class,
            ProductPriceMFilter::class,
            ProductOrderByMFilter::class
        ];

        $query = app(Pipeline::class)
            ->send($query)
            ->through($filters)
            ->thenReturn();

        return $query->paginate($pagination);
    }

    public function getAllVariants(int $productId, int $variantId): Model
    {
        return Product::query()
            ->where('id', $productId)
            ->whereHas('variants', function ($q) use ($variantId) {
                $q->where('id', $variantId);
            })
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
