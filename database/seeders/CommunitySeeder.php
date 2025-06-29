<?php

namespace Database\Seeders;

use App\Models\Community;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        $communities = [
            ['name' => 'Army Veterans', 'description' => 'Community for Army veterans and their families', 'colour' => 'green'],
            ['name' => 'Navy Veterans', 'description' => 'Community for Navy veterans and their families', 'colour' => 'blue'],
            ['name' => 'Air Force Veterans', 'description' => 'Community for Air Force veterans and their families', 'colour' => 'sky'],
            ['name' => 'Marine Corps Veterans', 'description' => 'Community for Marine Corps veterans and their families', 'colour' => 'red'],
            ['name' => 'Coast Guard Veterans', 'description' => 'Community for Coast Guard veterans and their families', 'colour' => 'orange'],
            ['name' => 'Vietnam Veterans', 'description' => 'Community for Vietnam War veterans', 'colour' => 'emerald'],
            ['name' => 'Iraq War Veterans', 'description' => 'Community for Iraq War veterans', 'colour' => 'amber'],
            ['name' => 'Afghanistan Veterans', 'description' => 'Community for Afghanistan War veterans', 'colour' => 'purple'],
        ];

        foreach ($communities as $community) {
            Community::create($community);
        }
    }
}
