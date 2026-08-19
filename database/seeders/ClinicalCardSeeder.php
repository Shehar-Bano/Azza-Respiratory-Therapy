<?php

namespace Database\Seeders;

use App\Models\ClinicalCard;
use Illuminate\Database\Seeder;

class ClinicalCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicalCard::updateOrCreate(
            ['title' => 'Airway Assessment & Mallampati Score'],
            [
                'description' => 'Quick reference for difficult airway assessment and intubation protocols.',
                'image' => 'uploads/cards/images/airway_card_thumb.png',
                'document' => 'uploads/cards/documents/airway_assessment.pdf',
            ]
        );
    }
}
