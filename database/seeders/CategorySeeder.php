<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['category_type' => 'Curry', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Achievements', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Awards', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Sport', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Promotion', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Commission', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Birth', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Death', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Marriage', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Food', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Social', 'created_at' => now(), 'updated_at' => now()],
            ['category_type' => 'Other', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
