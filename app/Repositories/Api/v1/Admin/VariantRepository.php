<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\VariantInterface;
use App\Models\Variant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VariantRepository extends BaseRepository implements VariantInterface
{
    public function __construct(Variant $model)
    {
        parent::__construct($model);
    }

    public function getAll(int $pagination): LengthAwarePaginator
    {
        return $this->model::with(['images', 'branches', 'product', 'optionProductValues.optionValue'])->paginate($pagination);
    }

    public function getById(int $id): Model
    {
        return $this->model::with(['images', 'branches', 'product', 'optionProductValues.optionValue'])->findOrFail($id);
    }

    public function getAllShort(int $pagination, int $id): LengthAwarePaginator
    {
        return $this->model::where('product_id', $id)
            ->with(['images', 'optionProductValues.optionValue'])
            ->paginate($pagination);
    }

    public function create(array $variantData, array $images, array $variantFeatures, int $stockmin): Model
    {
        DB::beginTransaction();
        try{
            $variant = $this->model->create($variantData);
            $variant->branches()->attach(1, [
                'stock' => 0,
                'stock_min' => $stockmin,
            ]);

            foreach($images as $imagePath) {
                $variant->images()->create([
                    'path' => $imagePath
                ]);
            }

            $variant->optionProductValues()->sync($variantFeatures);

            DB::commit();

            return $variant->refresh()->load(['images', 'branches', 'product', 'optionProductValues.optionValue']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update(array $variantData, ?int $stockmin, ?array $images, ?array $variantFeatures, int $id): Model
    {
        DB::beginTransaction();
        try {
            $model = $this->getById($id);

            if (!empty($variantData)) {
                $model->update($variantData);
            }

            if (!is_null($stockmin)) {
                $model->branches()->updateExistingPivot(1, [
                    'stock_min' => $stockmin
                ]);
            }

            if (!empty($images)) {
                foreach($images as $imagepath) {
                    $model->images()->update([
                        'path' => $imagepath
                    ]);
                };
            }

            if (!empty($variantFeatures)) {
                $model->optionProductValues()->sync($variantFeatures);
            }

            DB::commit();
            return $model->refresh()->load(['images', 'branches', 'product', 'optionProductValues.optionValue']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
