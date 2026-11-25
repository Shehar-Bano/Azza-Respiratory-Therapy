<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Seed default clinical and account FAQs.
     */
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Clinical Calculators',
                'question' => 'How are clinical equations such as MAP and IBW computed?',
                'answer' => 'Equations conform strictly to standard respiratory care formulas (e.g. MAP = DBP + (SBP - DBP)/3; IBW Devine formula: 50kg + 2.3*(height_inches - 60) for males). Inputs are validated before outputting results.',
            ],
            [
                'category' => 'Clinical Calculators',
                'question' => 'Can I bookmark formulas for instant access?',
                'answer' => 'Yes. On any calculator card, tap the heart icon (♡) to bookmark it. Favorited calculators appear in the Quick Access grid on both the Home and Calculator screens.',
            ],
            [
                'category' => 'Subscriptions',
                'question' => 'How do I unlock all clinical calculators and seminar cards?',
                'answer' => 'Navigate to Settings > Membership Plans to view subscription tiers (Monthly, Quarterly, or Annual) powered securely by Paystack.',
            ],
            [
                'category' => 'Notifications',
                'question' => 'Why am I only receiving in-app notifications and no phone rings?',
                'answer' => 'You can manage which notifications ring your device by visiting Settings > Notification Channels. Enable push notifications for General Notification, Security Alert, Update, or Reminder to receive device rings.',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                ['category' => $faq['category'], 'answer' => $faq['answer']]
            );
        }
    }
}
