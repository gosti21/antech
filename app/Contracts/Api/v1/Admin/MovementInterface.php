<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Model;

interface MovementInterface extends BaseInterface
{
    public function createInflow(array $data): Model;
    public function createOutflow(array $data): Model;
}
