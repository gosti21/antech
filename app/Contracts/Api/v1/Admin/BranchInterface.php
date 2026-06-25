<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Model;

interface BranchInterface extends BaseInterface
{
    public function create(array $branchData, array $phoneData): Model;
    public function update(array $branchData, array $phoneData, int $id): Model;
}
