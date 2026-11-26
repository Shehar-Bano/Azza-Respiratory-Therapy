<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\SupportContact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminSupportAndFaqTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_view_and_update_support_contact_and_api_reflects_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->get('/admin/support-contact')
            ->assertStatus(200)
            ->assertSee('Support Contact');

        $this->actingAs($admin)
            ->put('/admin/support-contact', [
                'support_email' => 'helpdesk@azzatherapy.com',
                'support_phone' => '+966 11 999 8888',
                'working_hours' => 'Mon - Fri: 8:00 AM - 5:00 PM (AST)',
                'support_options' => "Direct Email Support\nLive Chat",
            ])
            ->assertRedirect(route('admin.support-contact.index'));

        $this->getJson('/api/support-contact')
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'support_email' => 'helpdesk@azzatherapy.com',
                    'support_phone' => '+966 11 999 8888',
                    'working_hours' => 'Mon - Fri: 8:00 AM - 5:00 PM (AST)',
                    'support_options' => ['Direct Email Support', 'Live Chat'],
                ],
            ]);
    }

    public function test_admin_can_perform_full_faq_crud(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->get('/admin/faqs')
            ->assertStatus(200)
            ->assertSee('FAQs Management');

        // Create
        $this->actingAs($admin)
            ->post('/admin/faqs', [
                'category' => 'Ventilator Settings',
                'question' => 'What is the initial tidal volume for ARDS?',
                'answer' => '6 mL/kg of predicted body weight.',
            ])
            ->assertRedirect(route('admin.faqs.index'));

        $faq = Faq::where('question', 'What is the initial tidal volume for ARDS?')->firstOrFail();

        // Update
        $this->actingAs($admin)
            ->put('/admin/faqs/' . $faq->id, [
                'category' => 'Ventilator Settings',
                'question' => 'What is the target tidal volume for ARDS?',
                'answer' => '4 to 8 mL/kg of predicted body weight.',
            ])
            ->assertRedirect(route('admin.faqs.index'));

        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'question' => 'What is the target tidal volume for ARDS?',
        ]);

        // Delete
        $this->actingAs($admin)
            ->delete('/admin/faqs/' . $faq->id)
            ->assertRedirect(route('admin.faqs.index'));

        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }

    public function test_admin_can_create_and_view_user_with_profile_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $email = 'admin.created.' . uniqid() . '@hospital.org';

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertStatus(200)
            ->assertSee('Add New User');

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Dr. Test User',
                'email' => $email,
                'phone' => '+966539301053',
                'city' => 'Riyadh',
                'location' => 'King Fahd Rd, Riyadh',
                'latitude' => '24.713552',
                'longitude' => '46.675296',
                'role' => 'user',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'phone' => '+966539301053',
            'city' => 'Riyadh',
            'location' => 'King Fahd Rd, Riyadh',
        ]);
    }

    public function test_admin_can_view_update_and_delete_customer_feedback(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $user = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $feedback = \App\Models\UserFeedback::create([
            'user_id' => $user->id,
            'message' => 'Please add pediatric thresholds.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get('/admin/feedbacks')
            ->assertStatus(200)
            ->assertSee('Customer Feedback')
            ->assertSee('Please add pediatric thresholds.');

        $this->actingAs($admin)
            ->put('/admin/feedbacks/' . $feedback->id . '/status', ['status' => 'resolved'])
            ->assertRedirect(route('admin.feedbacks.index'));

        $this->assertDatabaseHas('user_feedbacks', [
            'id' => $feedback->id,
            'status' => 'resolved',
        ]);

        $this->actingAs($admin)
            ->delete('/admin/feedbacks/' . $feedback->id)
            ->assertRedirect(route('admin.feedbacks.index'));

        $this->assertDatabaseMissing('user_feedbacks', ['id' => $feedback->id]);
    }
}
