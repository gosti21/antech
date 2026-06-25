<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\UserSInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserSRepository implements UserSInterface
{
    public function getById(int $id): Model
    {
        return User::findOrFail($id);
    }
}
