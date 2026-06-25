<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\MovementInterface;
use App\Models\Movement;
use App\Repositories\Api\v1\Admin\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MovementRepository extends BaseRepository implements MovementInterface
{
    public function __construct(Movement $model)
    {
        parent::__construct($model);
    }

    public function getById(int $id): Model
    {
        return $this->model::with(['branchVariants'])->findOrFail($id);
    }

    public function createInflow(array $data): Model
    {
        return DB::transaction(
            function () use ($data) {
                $movement = $this->model->create([
                    'type' => $data['type'],
                    'reason' => $data['reason'],
                    'detail_transaction' => $data['detail_transaction'],
                ]);

                foreach ($data['variants'] as $variant) {
                    $movement->branchVariants()->attach(
                        $variant['branch_variant_id'],
                        ['quantity' => $variant['quantity']]
                    );

                    $movement->branchVariants()
                        ->where('branch_variant_id', $variant['branch_variant_id'])
                        ->increment('stock', $variant['quantity']);
                }

                return $movement->refresh();
            }
        );
    }

    public function createOutflow(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $movement = $this->model->create([
                'type' => $data['type'],
                'reason' => $data['reason'],
                'detail_transaction' => $data['detail_transaction'],
            ]);

            foreach ($data['variants'] as $variant) {
                $movement->branchVariants()->attach(
                    $variant['branch_variant_id'],
                    ['quantity' => $variant['quantity']]
                );

                $movement->branchVariants()
                    ->where('branch_variant_id', $variant['branch_variant_id'])
                    ->decrement('stock', $variant['quantity']);
            }

            return $movement->refresh();
        });
    }
}
