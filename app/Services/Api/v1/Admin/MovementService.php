<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\MovementInterface;
use App\Exceptions\Api\v1\General\InsufficentStockException;
use App\Models\BranchVariant;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<MovementInterface>
 */
class MovementService extends BaseService
{
    public function __construct(MovementInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        if ($data['type'] === 'outflow') {
            foreach ($data['variants'] as $variantData) {
                $variant = BranchVariant::find($variantData['branch_variant_id']);

                if ($variant->stock <= $variantData['quantity']) {
                    throw new InsufficentStockException();
                }
            }
            return $this->repository->createOutflow($data);
        }

        return $this->repository->createInflow($data);
    }
}
