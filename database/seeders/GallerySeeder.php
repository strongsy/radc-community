<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('galleries')->insert([
            ['gallery_title' => 'Events', 'gallery_desc' => 'Events Gallery', 'created_at' => now(), 'updated_at' => now()],
            ['gallery_title' => 'Stories', 'gallery_desc' => 'Stories Gallery', 'created_at' => now(), 'updated_at' => now()],
            ['gallery_title' => 'News', 'gallery_desc' => 'News Gallery', 'created_at' => now(), 'updated_at' => now()],
            ['gallery_title' => 'Articles', 'gallery_desc' => 'Articles Gallery', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
