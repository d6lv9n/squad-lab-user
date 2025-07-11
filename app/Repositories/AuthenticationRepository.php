<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationRepository implements AuthenticationRepositoryInterface
{
    protected $lockMinutes = 15;
    protected $maxAttempts = 5;

    public function checkAttempts(string $cacheKey): int
    {
        // Get the cache attempts
        $attempts = (int) Cache::get($cacheKey, 0);

        // If the attempts equals or more than {x}
        if ($attempts >= $this->maxAttempts) {
            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$this->lockMinutes} minutes."]
            ]);
        }

        return $attempts;
    }

    public function clearAttempts(string $cacheKey): void
    {
        Cache::forget($cacheKey);
    }

    public function createToken(User $user): string
    {
        // return JWTAuth::fromUser($user);
        return auth()->login($user);
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function findUserAndVerifyPassword(array $data, string $cacheKey, int $attempts): User
    {
        $cacheExpires = Carbon::now('UTC')->addMinutes($this->lockMinutes);

        $user = User::query()
                ->select('id', 'name', 'password')
                ->where('email', $data['email'])
                ->first();

        // If user does not exists or hash check false
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            // We increment the attempts
            $totalAttempts = $attempts + 1;

            // Rewrite the cache attempts
            Cache::put($cacheKey, $totalAttempts, $cacheExpires);

            throw ValidationException::withMessages([
                'email' => ['Invalid email address.']
            ]);
        }

        return $user;
    }

    public function logoutUser(): void
    {
        // auth()->logout(); // or JWTAuth::invalidate(JWTAuth::getToken());

        // Pass true to force the token to be blacklisted "forever"
        // https://jwt-auth.readthedocs.io/en/develop/auth-guard/#logout
        auth()->logout(true);
    }
}
