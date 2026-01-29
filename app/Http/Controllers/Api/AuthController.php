<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->userRepository->register($request->validated());
        $token = JWTAuth::fromUser($user);

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'User successfully registered', 201);
    }

    /**
     * Log the user in and return a token.
     */
    public function login(LoginRequest $request)
    {
        if (!$token = $this->userRepository->attemptLogin($request->validated())) {
            return $this->errorResponse('Unauthorized', 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     */
    public function me()
    {
        return $this->successResponse($this->userRepository->getCurrentUser());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        $this->userRepository->logout();

        return $this->successResponse(null, 'Successfully logged out');
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        return $this->respondWithToken($this->userRepository->refresh());
    }

    /**
     * Helper to format token response.
     */
    protected function respondWithToken($token)
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }
}
