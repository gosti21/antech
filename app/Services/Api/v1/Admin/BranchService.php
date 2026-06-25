<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\BranchInterface;
use App\Exceptions\Api\v1\InternalServerErrorException;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

/**
 * @extends BaseService<BranchInterface>
 */
class BranchService extends BaseService
{
    public function __construct(BranchInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $branchData = Arr::only($data, [
            'name',
            'email',
        ]);
        $phoneData = Arr::only($data, [
            'prefix',
            'number'
        ]);

        try{
            return $this->repository->create($branchData, $phoneData);
        } catch (\Exception $e) {
            throw new InternalServerErrorException(
                'No se pudo crear la sucursal',
                $e->getMessage()
            );
        }
    }

    public function update(array $data, int $id): Model
    {
        $branchData = Arr::only($data, [
            'name',
            'email',
        ]);

        $phoneData = Arr::only($data, [
            'prefix',
            'number'
        ]);

        try {
            return $this->repository->update($branchData, $phoneData, $id);
        } catch (ModelNotFoundException $e){
            throw new NotFoundException();
        } catch (\Exception $e) {
            throw new InternalServerErrorException(
                'No se pudo actualizar la sucursal',
                $e->getMessage()
            );
        }
    }
}
