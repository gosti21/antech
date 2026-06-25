<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface OptionInterface extends BaseInterface
{
    public function create(array $data): Model;
    public function update(array $optionData, array $optionValuesData, int $id): Model;
    public function getOptionValues(int $id): Collection;
}
