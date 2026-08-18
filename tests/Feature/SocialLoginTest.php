<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_login_success()
    {
        $response = $this->postJson('/api/social-login', [
            'name' => 'Muneeb Ahmed',
            'email' => 'muneeb@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Login successfully',
                'user' => [
                    'name' => 'Muneeb Ahmed',
                    'email' => 'muneeb@example.com',
                ],
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'token',
                'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'muneeb@example.com',
            'name' => 'Muneeb Ahmed',
        ]);
    }

    public function test_social_login_validation_failure_missing_email()
    {
        $response = $this->postJson('/api/social-login', [
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
