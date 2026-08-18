<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article::updateOrCreate(
            ['title' => 'Arterial Blood Gas (ABG) Analysis'],
            [
                'description' => 'Comprehensive guide on pH, PaCO2, and HCO3 interpretation in acute respiratory failure.',
                'image' => 'abg_article.png',
                'document' => 'abg_clinical_manual.pdf',
            ]
        );
    }
}
