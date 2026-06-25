<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\BranchInterface;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BranchRepository extends BaseRepository implements BranchInterface
{
    public function __construct(Branch $model)
    {
        parent::__construct($model);
    }

    public function create(array $branchData, array $phoneData): Model
    {
        DB::beginTransaction();
        try{
            $branch = Branch::create($branchData);
            $branch->phone()->create([
                'prefix_id' => $phoneData['prefix'],
                'number' => $phoneData['number'],
            ]);

            DB::commit();

            return $branch->refresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update(array $branchData, array $phoneData, int $id): Model
    {
        DB::beginTransaction();
        try {
            $branch = $this->getById($id);

            if (!empty($branchData)) {
                $branch->update($branchData);
            }

            if (!empty($phoneData)) {
                $branch->phone()->update($phoneData);
            }

            DB::commit();
            return $branch->refresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
