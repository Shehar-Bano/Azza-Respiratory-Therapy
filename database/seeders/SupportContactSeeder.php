<?php

namespace Database\Seeders;

use App\Models\SupportContact;
use Illuminate\Database\Seeder;

class SupportContactSeeder extends Seeder
{
    /**
     * Seed default support contact settings.
     */
    public function run(): void
    {
        if (SupportContact::count() === 0) {
            SupportContact::create(SupportContact::defaultValues());
        }
    }
}
