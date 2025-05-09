<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('statuses')->insert([
            ['status_type' => 'Pending', 'status_desc' => 'Requires approval by Moderator', 'created_at' => now(), 'updated_at' => now()],
            ['status_type' => 'Approved', 'status_desc' => 'Approved for release by Moderator', 'created_at' => now(), 'updated_at' => now()],
            ['status_type' => 'Rejected', 'status_desc' => 'Rejected for release by Moderator', 'created_at' => now(), 'updated_at' => now()],
            ['status_type' => 'Draft', 'status_desc' => 'Still being worked on by the creator', 'created_at' => now(), 'updated_at' => now()],
            ['status_type' => 'Published', 'status_desc' => 'Visible to all users', 'created_at' => now(), 'updated_at' => now()],
            ['status_type' => 'Archived', 'status_desc' => 'No longer active but saved', 'created_at' => now(), 'updated_at' => now()],
            ['status_type' => 'Cancelled', 'status_desc' => 'Event has been cancelled', 'created_at' => now(), 'updated_at' => now()],
            ['status_type' => 'Completed', 'status_desc' => 'Event has finished', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

}
