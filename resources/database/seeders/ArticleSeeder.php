<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::first();
        $categoryId = $category ? $category->id : 1;

        Article::updateOrCreate(
            ['title' => 'Arterial Blood Gas (ABG) Analysis'],
            [
                'category_id' => $categoryId,
                'description' => 'Comprehensive guide on pH, PaCO2, and HCO3 interpretation in acute respiratory failure.',
                'image' => 'abg_article.png',
                'document' => 'abg_clinical_manual.pdf',
            ]
        );

        Article::updateOrCreate(
            ['title' => 'PEEP Titration Protocol'],
            [
                'category_id' => $categoryId,
                'description' => 'Step-by-step protocol for titration and lung recruitment strategies.',
                'image' => 'peep_thumb.png',
                'document' => 'peep_protocol.pdf',
            ]
        );
    }
}
