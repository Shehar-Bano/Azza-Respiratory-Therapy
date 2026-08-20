<?php

namespace Database\Seeders;

use App\Models\SubscriptionFeature;
use Illuminate\Database\Seeder;

class SubscriptionFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'id' => 1,
                'slug' => 'calculator_unlocked',
                'title' => 'Clinical Calculator Unlocked',
                'description' => 'Unlocks access to all Clinical Calculator formulas.',
            ],
            [
                'id' => 2,
                'slug' => 'articles_unlocked',
                'title' => 'Full Articles Access',
                'description' => 'Unlocks access to all Respiratory Therapy articles.',
            ],
            [
                'id' => 3,
                'slug' => 'cards_unlocked',
                'title' => 'Flashcards Access',
                'description' => 'Unlocks full access to Clinical Flashcards.',
            ],
            [
                'id' => 4,
                'slug' => 'classes_unlocked',
                'title' => 'All RT Classes & Subcategories',
                'description' => 'Unlocks full access to all RT classes and subcategories.',
            ],
            [
                'id' => 5,
                'slug' => 'validity_30_days',
                'title' => '30 Days Validity',
                'description' => 'Grants 30 days subscription access validity.',
            ],
            [
                'id' => 6,
                'slug' => 'formula_1st_free',
                'title' => '1st Formula Free',
                'description' => 'Free access to the 1st Clinical Calculator formula.',
            ],
            [
                'id' => 7,
                'slug' => 'article_1st_free',
                'title' => '1st Article Free',
                'description' => 'Free access to the 1st Respiratory Therapy article.',
            ],
            [
                'id' => 8,
                'slug' => 'card_1st_free',
                'title' => '1st Card Free',
                'description' => 'Free access to the 1st Clinical Card.',
            ],
        ];

        foreach ($features as $featureData) {
            SubscriptionFeature::updateOrCreate(
                ['id' => $featureData['id']],
                $featureData
            );
        }
    }
}
