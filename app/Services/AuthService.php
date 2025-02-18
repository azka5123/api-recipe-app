<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    protected UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $data): JsonResponse
    {
        try {
            if (!Auth::attempt($data)) {
                return ResponseHelper::error('Invalid credentials', 401);
            }
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseHelper::success('Login Success', [
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function register(array $data): JsonResponse
    {
        try {
            $user = $this->userRepository->register($data);
            $token = $user->createToken('auth_token')->plainTextToken;
            return ResponseHelper::success('Registration successful', [
                'token' => $token
            ]);
        } catch (\Exception $e) {
            if ($e->getCode() == 23000) {
                return ResponseHelper::error('Email already register', 500);
            }
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->tokens()->delete();
            }
            return ResponseHelper::success('logout successful');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function forgotPassword(array $data): JsonResponse
    {
        try {
            $user = $this->userRepository->findByMail($data['email']);
            if (!$user) {
                return ResponseHelper::error('Email not found', statusCode: 404);
            }
            $code = sprintf('%04d', random_int(0, 9999));
            Mail::to($user->email)->send(new ResetPasswordMail($code));

            $this->userRepository->createResetToken($user->email, $code);

            return ResponseHelper::success('Reset link sent to your email.', );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function verifyResetCode(array $data): JsonResponse
    {
        try {
            $tokenData = $this->userRepository->checkToken($data['email'], $data['code'], now()->subMinutes(env('TOKEN_RESET_TIME')));
            if (!$tokenData) {
                return ResponseHelper::error('Invalid or expired reset code.', 401);
            }

            return ResponseHelper::success('Code verified successfully.', [
                'email' => $tokenData->email,
                'code' => $tokenData->token,
            ]);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function resetPassword(array $data): JsonResponse
    {
        try {
            $dataToken = $this->userRepository->checkToken($data['email'], $data['code'], now()->subMinutes(env('TOKEN_RESET_TIME')));

            if (!$dataToken) {
                return ResponseHelper::error('Invalid or expired reset code.', 401);
            }

            $this->userRepository->updateUserPassword($data['email'], $data['password']);

            $this->userRepository->resetTokenUser($data['email'], $data['code']);

            return ResponseHelper::success('Password reset successfully.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }


}
