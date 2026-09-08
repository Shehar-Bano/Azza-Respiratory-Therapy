<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_default_login_method_new_user_success()
    {
        $response = $this->postJson('/api/social-login', [
            'login_method' => 'default',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Login successfully',
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'login_method' => 'default',
                ],
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'token',
                'user' => ['id', 'name', 'email', 'login_method', 'created_at', 'updated_at'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'login_method' => 'default',
        ]);
    }

    public function test_default_login_method_missing_password_validation_failure()
    {
        $response = $this->postJson('/api/social-login', [
            'login_method' => 'default',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 400,
                'message' => [
                    'The password field is required when login method is default.',
                ],
            ]);
    }

    public function test_default_login_method_existing_user_incorrect_password()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('secret123'),
            'login_method' => 'default',
        ]);

        $response = $this->postJson('/api/social-login', [
            'login_method' => 'default',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 400,
                'message' => [
                    'Invalid password.',
                ],
            ]);
    }

    public function test_google_login_method_success_without_password()
    {
        $response = $this->postJson('/api/social-login', [
            'login_method' => 'google',
            'name' => 'Google User',
            'email' => 'googleuser@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Login successfully',
                'user' => [
                    'name' => 'Google User',
                    'email' => 'googleuser@example.com',
                    'login_method' => 'google',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@example.com',
            'name' => 'Google User',
            'login_method' => 'google',
        ]);
    }

    public function test_apple_login_method_with_hyphenated_parameter()
    {
        $response = $this->postJson('/api/social-login', [
            'login-method' => 'apple',
            'name' => 'Apple User',
            'email' => 'appleuser@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Login successfully',
                'user' => [
                    'name' => 'Apple User',
                    'email' => 'appleuser@example.com',
                    'login_method' => 'apple',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'appleuser@example.com',
            'name' => 'Apple User',
            'login_method' => 'apple',
        ]);
    }

    public function test_social_login_validation_failure_invalid_login_method()
    {
        $response = $this->postJson('/api/social-login', [
            'login_method' => 'facebook',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 400,
                'message' => [
                    'The selected login method is invalid.',
                ],
            ]);
    }

    public function test_social_login_validation_failure_missing_email()
    {
        $response = $this->postJson('/api/social-login', [
            'login_method' => 'google',
            'name' => 'Muneeb Ahmed',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 400,
                'message' => [
                    'The email field is required.',
                ],
            ]);
    }

    public function test_social_login_validation_failure_missing_name()
    {
        $response = $this->postJson('/api/social-login', [
            'login_method' => 'google',
            'email' => 'muneeb@example.com',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 400,
                'message' => [
                    'The name field is required.',
                ],
            ]);
    }
}
