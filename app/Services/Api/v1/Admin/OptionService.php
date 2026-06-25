<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\OptionInterface;
use App\Exceptions\Api\v1\InternalServerErrorException;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

/**
 * @extends BaseService<OptionInterface>
 */
class OptionService extends BaseService
{
    public function __construct(OptionInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(array $data, int $id): Model
    {
        $optionData = Arr::only($data, [
            'name',
            'status',
        ]);

        $optionValuesData = Arr::only($data, ['option_values']);

        try {
            return $this->repository->update($optionData, $optionValuesData, $id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        } catch (\Exception $e) {
            throw new InternalServerErrorException(
                'No se pudo actualizar el producto',
                $e->getMessage()
            );
        }
    }

    public function getOptionValues(int $id): Collection
    {
        try {
            return $this->repository->getOptionValues($id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
