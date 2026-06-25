<?php

namespace App\Repositories\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\UserMInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserMRepository implements UserMInterface
{
    public function getById(int $id): Model
    {
        return User::findOrFail($id);
    }
}
