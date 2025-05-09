<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class AllergySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('allergies')->insert([
            ['allergy_type' => 'none', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'peanuts', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'shellfish', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'wheat', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'tree nuts', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'milk', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'fish', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'eggs', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'soy', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'nuts', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'dairy', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'gluten', 'created_at' => now(), 'updated_at' => now()],
            ['allergy_type' => 'other', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
