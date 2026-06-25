<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\OptionValueInterface;
use App\Models\OptionValue;
use Illuminate\Database\Eloquent\Model;

class OptionValueRepository implements OptionValueInterface
{
    public function getById(int $id): Model
    {
        return OptionValue::findOrFail($id);
    }

    public function create(array $data): Model
    {
        return OptionValue::create($data)->refresh();
    }
}
