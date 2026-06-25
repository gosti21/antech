<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\DistrictInterface;
use App\Models\District;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DistrictRepository extends BaseRepository implements DistrictInterface
{
    public function __construct(District $model)
    {
        parent::__construct($model);
    }

    public function getAll(int $pagination): LengthAwarePaginator
    {
        return $this->model::with(['province', 'shippingRate'])->paginate($pagination);
    }

    public function getById(int $id): Model
    {
        return $this->model::with(['province', 'shippingRate'])->findOrFail($id);
    }

    public function create(array $districData, array $shippingRateData): Model
    {
        DB::beginTransaction();
        try {
            $distric = District::create($districData);
            $distric->shippingRate()->create([
                'delivery_price' => $shippingRateData['delivery_price'],
                'min_delivery_days' => $shippingRateData['min_delivery_days'],
                'max_delivery_days' => $shippingRateData['max_delivery_days'],
            ]);

            DB::commit();

            return $distric->refresh()->load(['province', 'shippingRate']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update(array $districData, array $shippingRateData, int $id): Model
    {
        DB::beginTransaction();
        try {
            $distric = $this->getById($id);

            if (!empty($districData)) {
                $distric->update($districData);
            }

            if (!empty($shippingRateData)) {
                $distric->shippingRate()->update($shippingRateData);
            }

            DB::commit();
            return $distric->refresh()->load(['province', 'shippingRate']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
