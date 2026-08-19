<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Mechanical Ventilation',
            'Arterial Blood Gas',
            'Airway Management',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['category_name' => $name]
            );
        }
    }
}
