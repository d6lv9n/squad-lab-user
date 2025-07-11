<?php

namespace App\Repositories;

use App\Models\User;

interface AuthenticationRepositoryInterface
{
    public function checkAttempts(string $cacheKey);

    public function clearAttempts(string $cacheKey);

    public function createToken(User $user);

    public function createUser(array $data);

    public function findUserAndVerifyPassword(array $data, string $cacheKey, int $attempts);

    public function logoutUser();
}
