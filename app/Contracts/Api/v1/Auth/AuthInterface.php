<?php

namespace App\Contracts\Api\v1\Auth;

use App\Models\User;

interface AuthInterface {
    public function finByEmail(string $email): ?User;
    public function register(array $data): User;
}
