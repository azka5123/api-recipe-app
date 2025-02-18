<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyResetCodeRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends MasterApiController
{

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle login request
     *
     * @unauthenticated
     *
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    /**
     * Handle registration request
     *
     * @unauthenticated
     *
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request->validated());
    }

    /**
     * Handle logout request (Invalidates the token).
     *
     * @authenticated
     *
     */
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }

    /**
     * Handle forgot password request (Sends password reset link to the given user).
     *
     * @unauthenticated
     *
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        return $this->authService->forgotPassword($request->validated());
    }

    /**
     * Handle verify reset code request (Checks if the provided code is valid and not expired).
     *
     * @unauthenticated
     *
     */
    public function verifyResetCode(VerifyResetCodeRequest $request): JsonResponse
    {
        return $this->authService->verifyResetCode($request->validated());
    }

    /**
     * Reset password (Updates user password based on provided code and new password).
     *
     * @unauthenticated
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->authService->resetPassword($request->validated());
    }
}
