<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::updateOrCreate(
            ['plan_id' => '0'],
            [
                'title' => 'Free Plan',
                'price' => '0.00',
                'price_usd' => '0.00',
                'price_sar' => '0.00',
                'currency' => 'USD',
                'currency_usd' => 'USD',
                'currency_sar' => 'SAR',
                'duration_days' => 0,
                'access' => 'to 1st formula, 1st article, and 1st card only',
                'feature_ids' => [6, 7, 8],
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['plan_id' => '1'],
            [
                'title' => 'Half Subscription',
                'price' => '19.99',
                'price_usd' => '19.99',
                'price_sar' => '74.96',
                'currency' => 'USD',
                'currency_usd' => 'USD',
                'currency_sar' => 'SAR',
                'duration_days' => 30,
                'access' => 'to Calculator, Articles and Cards Only',
                'feature_ids' => [1, 2, 3, 5],
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['plan_id' => '2'],
            [
                'title' => 'Full Subscription',
                'price' => '105.99',
                'price_usd' => '105.99',
                'price_sar' => '397.46',
                'currency' => 'USD',
                'currency_usd' => 'USD',
                'currency_sar' => 'SAR',
                'duration_days' => 30,
                'access' => 'to all RT classes and complete content',
                'feature_ids' => [1, 2, 3, 4, 5],
            ]
        );
    }
}
