<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\LoginRequest;
use App\Http\Requests\Api\v1\SignupRequest;
use App\Repositories\AuthenticationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuthenticationController extends Controller implements HasMiddleware
{
    private $authenticationRepositoryInterface;

    public function __construct(
        AuthenticationRepositoryInterface $authenticationRepositoryInterface,
    ) {
        $this->authenticationRepositoryInterface = $authenticationRepositoryInterface;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', only: ['logout', 'profile']),
            new Middleware('guest:api', only: ['login', 'signup']),
        ];
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $cacheKey = 'login:' . sha1($request->ip() . '|' . $request->email);

        $attempts = $this->authenticationRepositoryInterface->checkAttempts($cacheKey);

        $user = $this->authenticationRepositoryInterface->findUserAndVerifyPassword($request->only(['email', 'password']), $cacheKey, $attempts);

        $token = $this->authenticationRepositoryInterface->createToken($user);

        $this->authenticationRepositoryInterface->clearAttempts($cacheKey);

        return response()->json([
            'profile' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authenticationRepositoryInterface->logoutUser();

        return response()->json(null, 204);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'profile' => auth()->user()
        ], 200);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        return response()->json([
            'token' => auth()->refresh(),
        ]);
    }

    public function signup(SignupRequest $request): JsonResponse
    {
        
    }
}
