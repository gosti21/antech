<?php

namespace App\Repositories\Api\v1\Auth;

use App\Contracts\Api\v1\Auth\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthInterface
{
    public function finByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function register (array $data): User
    {
        return DB::transaction(function () use ($data){
            $user = User::create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'date_birth' => $data['date_birth'] ?? null,
            ]);

            $user->assignRole('user');

            return $user;
        });
    }
}
