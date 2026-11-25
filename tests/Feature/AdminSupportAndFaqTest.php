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
}
