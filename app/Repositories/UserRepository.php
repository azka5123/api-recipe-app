<?php

namespace App\Repositories;

use App\Models\User;
use Hash;

class UserRepository
{
    public function findByMail(string $email): ?User
    {
        return User::where("email", $email)->first();
    }

    public function register(array $data): User
    {
        $data["password"] = Hash::make($data["password"]);
        return User::create($data);
    }


    public function updateUserPassword(string $email, string $password): void
    {
        User::where('email', $email)->update([
            'password' => \Hash::make($password),
        ]);
    }

    public function createResetToken(string $email, string $code): void
    {
        \DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $code,
            'created_at' => now(),
        ]);
    }

    public function resetTokenUser(string $email, string $token): void
    {
        \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->delete();
    }

    public function checkToken($email, $code, $created_at)
    {
        return \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $code)
            ->where('created_at', '>=', $created_at)
            ->first();
    }
}