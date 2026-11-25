<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiSpecificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_support_contact_and_faqs_endpoints(): void
    {
        $this->getJson('/api/support-contact')
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'support_email' => 'support@azzatherapy.com',
                ],
            ]);

        $this->getJson('/api/faqs?category=Clinical+Calculators&search=MAP')
            ->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonFragment(['category' => 'Clinical Calculators']);
    }

    public function test_register_verify_otp_forgot_and_reset_password_flow(): void
    {
        $email = 'sarah.test.' . uniqid() . '@hospital.org';

        $registerRes = $this->postJson('/api/register', [
            'name' => 'Dr. Sarah Jenkins',
            'email' => $email,
            'password' => 'SecurePassword#2026',
            'password_confirmation' => 'SecurePassword#2026',
            'phone' => '+966539301053',
        ]);

        $registerRes->assertStatus(201)
            ->assertJson([
                'status' => true,
                'data' => ['email' => $email],
            ]);

        $otp = $registerRes->json('otp');
        $this->assertNotEmpty($otp);

        $verifyRes = $this->postJson('/api/verify-email-otp', [
            'email' => $email,
            'otp' => $otp,
        ]);

        $verifyRes->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['token', 'user']);

        // Forgot password -> verify reset OTP -> reset password (without Bearer header)
        $forgotRes = $this->postJson('/api/forgot-password', ['email' => $email]);
        $forgotRes->assertStatus(200)->assertJson(['status' => true]);
        $resetOtp = $forgotRes->json('otp');

        $verifyResetRes = $this->postJson('/api/verify-reset-otp', [
            'email' => $email,
            'otp' => $resetOtp,
        ]);
        $verifyResetRes->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['reset_token']);

        $resetToken = $verifyResetRes->json('reset_token');

        $this->postJson('/api/reset-password', [
            'token' => $resetToken,
            'password' => 'NewSecurePassword#2026',
            'password_confirmation' => 'NewSecurePassword#2026',
        ])->assertStatus(200)->assertJson(['status' => true]);
    }

    public function test_update_profile_get_profile_and_delete_account(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $avatar = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $updateRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/user/update-profile', [
                'name' => 'Dr. Sarah Updated',
                'email' => $user->email,
                'phone' => '+966539301053',
                'location' => 'King Fahd Rd, Riyadh',
                'city' => 'Riyadh',
                'latitude' => 24.713552,
                'longitude' => 46.675296,
                'profile_image' => $avatar,
            ], ['Accept' => 'application/json']);

        $updateRes->assertStatus(200)
            ->assertJson([
                'status' => true,
                'user' => [
                    'name' => 'Dr. Sarah Updated',
                    'city' => 'Riyadh',
                    'phone' => '+966539301053',
                ],
            ]);

        $savedPath = $user->fresh()->profile_image;
        $this->assertNotEmpty($savedPath);
        $this->assertTrue(File::exists(public_path($savedPath)));

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user/profile')
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'user' => [
                    'id' => $user->id,
                    'city' => 'Riyadh',
                ],
            ])
            ->assertJsonStructure(['user' => ['plan_id', 'subscription' => ['status', 'plan_name', 'expires_at']]]);

        // Delete account
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/user/delete-account', ['reason' => 'Testing'])
            ->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertFalse(File::exists(public_path($savedPath)));
    }

    public function test_feedback_and_notification_preferences(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/feedback/submit', [
                'message' => 'Please add pediatric thresholds for the ROX Index calculator.',
            ])
            ->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertDatabaseHas('user_feedbacks', [
            'user_id' => $user->id,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/user/notification-preferences')
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'push_enabled' => true,
                    'channels' => [
                        'General Notification' => true,
                        'Reminder' => true,
                        'Update' => true,
                        'Security Alert' => true,
                    ],
                ],
            ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/notification-preferences', [
                'push_enabled' => true,
                'channels' => [
                    'General Notification' => false,
                    'Reminder' => true,
                    'Update' => false,
                    'Security Alert' => true,
                ],
            ])
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'push_enabled' => true,
                    'channels' => [
                        'General Notification' => false,
                        'Reminder' => true,
                        'Update' => false,
                        'Security Alert' => true,
                    ],
                ],
            ]);

        $fresh = $user->fresh();
        $this->assertFalse($fresh->isPushAllowedForType('general_notification'));
        $this->assertTrue($fresh->isPushAllowedForType('reminder'));
        $this->assertFalse($fresh->isPushAllowedForType('update'));
        $this->assertTrue($fresh->isPushAllowedForType('security_alert'));
    }

    public function test_catalog_and_verify_payment_endpoints(): void
    {
        $category = Category::create(['category_name' => 'Test Ventilation']);

        $this->getJson('/api/category/get')
            ->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['categories']);

        $this->getJson('/api/category/artical/get?category_id=' . $category->id)
            ->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['data' => ['current_page', 'last_page', 'total', 'data']]);

        $this->getJson('/api/article/get')
            ->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['data' => ['current_page', 'last_page', 'total', 'data']]);

        $this->getJson('/api/card/get')
            ->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['data' => ['current_page', 'last_page', 'total', 'data']]);

        $plan = SubscriptionPlan::firstOrCreate(
            ['plan_id' => '2'],
            [
                'title' => 'Pro Annual Plan',
                'price' => 199.99,
                'currency' => 'SAR',
                'duration_days' => 365,
            ]
        );

        $user = User::factory()->create(['email_verified_at' => now()]);

        config(['services.paystack.secret_key' => 'sk_test_fake']);
        Http::fake([
            'https://api.paystack.co/transaction/verify/pstk_ref_982348234' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 19999,
                    'currency' => 'SAR',
                    'channel' => 'card',
                ],
            ], 200),
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/verify-payment', [
                'reference' => 'pstk_ref_982348234',
                'plan_id' => 2,
            ])
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Payment verified and subscription activated successfully.',
            ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/user/subscription-status?user_id=' . $user->id)
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'is_active' => true,
                    'active_plan_id' => '2',
                ],
            ]);
    }

    public function test_swagger_documentation_endpoint_loads(): void
    {
        $this->get('/api/documentation')->assertStatus(200);
        $this->get('/docs')->assertStatus(200);
    }
}
