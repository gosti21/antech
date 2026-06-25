<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Model;

interface OptionValueInterface
{
    public function getById(int $id): Model;
    public function create(array $data): Model;
}
