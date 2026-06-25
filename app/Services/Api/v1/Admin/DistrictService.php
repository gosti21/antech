<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\DistrictInterface;
use App\Exceptions\Api\v1\InternalServerErrorException;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

/**
 * @extends BaseService<DistrictInterface>
 */
class DistrictService extends BaseService
{
    public function __construct(DistrictInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $districData = Arr::only($data, [
            'name',
            'province_id',
        ]);
        $shippingRateData = Arr::only($data, [
            'delivery_price',
            'min_delivery_days',
            'max_delivery_days'
        ]);

        try {
            return $this->repository->create($districData, $shippingRateData);
        } catch (\Exception $e) {
            throw new InternalServerErrorException(
                'No se pudo crear el distrito',
                $e->getMessage()
            );
        }
    }

    public function update(array $data, int $id): Model
    {
        $districData = Arr::only($data, [
            'name',
            'province_id',
            'status'
        ]);
        $shippingRateData = Arr::only($data, [
            'delivery_price',
            'min_delivery_days',
            'max_delivery_days'
        ]);

        try {
            return $this->repository->update($districData, $shippingRateData, $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        } catch (\Exception $e) {
            throw new InternalServerErrorException(
                'No se pudo actualizar el distrito',
                $e->getMessage()
            );
        }
    }
}
