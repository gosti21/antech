<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\ProductSInterface;
use App\Filters\Api\v1\Shop\Products\ProductCategorySFilter;
use App\Filters\Api\v1\Shop\Products\ProductSubcategorySFilter;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;

class ProductSRepository implements ProductSInterface
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
            ProductCategorySFilter::class,
            ProductSubcategorySFilter::class,
        ];

        $query = app(Pipeline::class)
            ->send($query)
            ->through($filters)
            ->thenReturn();

        return $query->paginate($pagination);
    }

    public function getAllLasts(): Collection
    {
        return Product::query()
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
            ])->orderBy('created_at', 'asc') // últimos
            ->limit(16)
            ->get();
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
                'subcategory',
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
