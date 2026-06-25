<?php

namespace App\Services\Api\v1\Auth;

use App\Contracts\Api\v1\Auth\AuthInterface;
use App\Events\UserRegistered;
use App\Exceptions\Api\v1\Auth\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected AuthInterface $repository
    ){}

    public function login (array $credentials): array
    {
        $user = $this->repository->finByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException();
        }

        // Revocar tokens anteriores
        /* $user->tokens()->delete(); */

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function logout ($user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function register (array $data): array
    {
        $user = $this->repository->register($data);
        $token = $user->createToken('api-token')->plainTextToken;

        event(new UserRegistered($user));

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
