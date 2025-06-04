<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTitleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('event_titles')->insert([
            ['title' => 'Southern Group Curry Club', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Northern Group Curry Club', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Western Group Curry Club', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Eastern Group Curry Club', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Cenotaph', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Turning of the Page', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Officers Mess Function', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'RADC Officers Mess Function', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'RADC WO & Sgt Mess Function', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
