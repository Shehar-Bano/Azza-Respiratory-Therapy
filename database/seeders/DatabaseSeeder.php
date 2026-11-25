<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(ClinicalCardSeeder::class);
        $this->call(SubscriptionFeatureSeeder::class);
        $this->call(SubscriptionPlanSeeder::class);
        $this->call(SupportContactSeeder::class);
        $this->call(FaqSeeder::class);
    }
}
