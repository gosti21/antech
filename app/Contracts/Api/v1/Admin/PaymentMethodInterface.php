<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface PaymentMethodInterface
{
    public function getAllList(): Collection;
    public function getById(int $id): Model;
    public function update(?string $imagePath, int $id): Model;
}
