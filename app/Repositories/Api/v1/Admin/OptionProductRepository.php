<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\OptionProductInterface;
use App\Models\OptionProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OptionProductRepository implements OptionProductInterface
{
    public function getById(int $id): Model
    {
        return OptionProduct::findOrFail($id);
    }

    public function create(array $data): Model
    {
        DB::beginTransaction();
        try{
            $optionProduct = OptionProduct::create([
                'product_id' => $data['product_id'],
                'option_id' => $data['option_id']
            ]);

            foreach ($data['values'] as $value) {
                $optionProduct->optionValues()->attach(
                    $value['option_value_id']
                );
            }
            DB::commit();

            return $optionProduct->refresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function addValues(array $data): Model
    {
        DB::beginTransaction();
        try {
            $optionProduct = $this->getById($data['option_product_id']);
            $optionProduct->optionValues()->attach(
                $data['option_value_id']
            );
            DB::commit();

            return $optionProduct->refresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getAllValues(int $productId, int $optionId): Collection
    {

        return OptionProduct::where('product_id', $productId)
            ->where('option_id', $optionId)
            ->firstOrFail()
            ->optionValues()
            ->get()
            ->map(function ($value) {
                return [
                    'id' => $value->pivot->id, // 👈 ID DEL PIVOT
                    'id_value' => $value->id,
                    'description' => $value->description,
                ];
            });
    }
}
