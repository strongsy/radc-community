<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reasons')->insert([
            ['reason_type' => 'Harassment or Bullying', 'created_at' => now(), 'updated_at' => now()],
            ['reason_type' => 'Hate Speech', 'created_at' => now(), 'updated_at' => now()],
            ['reason_type' => 'Misinformation', 'created_at' => now(), 'updated_at' => now()],
            ['reason_type' => 'Violent Content', 'created_at' => now(), 'updated_at' => now()],
            ['reason_type' => 'Disturbing Content', 'created_at' => now(), 'updated_at' => now()],
            ['reason_type' => 'Sexual or Inappropriate', 'created_at' => now(), 'updated_at' => now()],
            ['reason_type' => 'Contains Personal Information', 'created_at' => now(), 'updated_at' => now()],
            ['reason_type' => 'Off Topic', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
