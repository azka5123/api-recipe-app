<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;
    protected $code;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'cobadulu769@gmail.com',
        ]);

        $response->assertStatus(200);
        Mail::assertSent(ResetPasswordMail::class, function ($mail) {

            $mailContent = $mail->render();
            $code2 = $this->extractResetCode($mailContent);
            $this->code = $code2;
            return true;
        });
    }
    public function test_register(): void
    {
        $response = $this->postJson('/api/register', [
            'email'    => $this->faker->unique()->safeEmail(),
            'password' => 'password123',
            'name'     => $this->faker->unique()->name(),
        ]);

        $response->assertStatus(200);
    }

    public function test_register_with_invalid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'email' => 'invalid-mail',
            'password'=> 'invalid-password-cause-didnt-have-number',
            'name'=> $this->faker->unique()->name(),
        ]);

        $response->assertStatus(422);
    }
    
    public function test_login(): void
    {
        $response = $this->postJson('/api/login',[
            'email' => 'cobadulu769@gmail.com',
            'password'=> '123456',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_invalid_data(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-mail',
            'password'=> 'invalid-password',
        ]);

        $response->assertStatus(422);
    }

    public function test_logout(): void
    {
        $auth = $this->postJson('/api/login',[
            'email' => 'cobadulu769@gmail.com',
            'password'=> '123456',
        ]);
        $token = $auth->json('data.token');
        $this->assertNotEmpty($token);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson('/api/logout');

        $response->assertStatus(200);
    }

    public function test_logout_with_invalid_data(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer 123"
        ])->postJson('/api/logout');

        $response->assertStatus(401);
    }

    public function test_verify_reset_code(): void
    {
        $response = $this->postJson('/api/verify-reset-code', [
            'email' => 'cobadulu769@gmail.com',
            'code' => $this->code,
        ]);

        $response->assertStatus(200);
    }

    public function test_verify_reset_code_with_invalid_code(): void
    {
        $response = $this->postJson('/api/verify-reset-code', [
            'email' => 'cobadulu769@gmail.com',
            'code' => 'invalid-code',
        ]);

        $response->assertStatus(401);
    }

    public function test_reset_password(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'email' => 'cobadulu769@gmail.com',
            'password' => '123456',
            'code' => $this->code,
        ]);

        $response->assertStatus(200);
    }

    public function test_reset_password_with_invalid_code(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'email' => 'cobadulu769@gmail.com',
            'password' => '12345678',
            'code' => 'invalid-code',
        ]);

        $response->assertStatus(status: 401);
    }

    public function test_forgot_password(): void
    {
        Mail::fake();
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'cobadulu769@gmail.com',
        ]);

        $response->assertStatus(200);
        Mail::assertSent(ResetPasswordMail::class, function ($mail) {

            $mailContent = $mail->render();
            $code2 = $this->extractResetCode($mailContent);
            $this->code = $code2;
            return true;
        });
    }

    public function test_forgot_password_with_invalid_data(): void
    {
        $response = $this->postJson('/api/forgot-password',[
            'email' => 'some@mail.com',
        ]);

        $response->assertStatus(404);
    }

    protected function extractResetCode($emailContent)
    {
        preg_match('/\d{4}/', $emailContent, $matches);
        return $matches[0] ?? null;
    }
}
