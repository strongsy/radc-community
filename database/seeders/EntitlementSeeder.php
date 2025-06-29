<?php

namespace Database\Seeders;

use App\Models\Entitlement;
use Illuminate\Database\Seeder;

class EntitlementSeeder extends Seeder
{
    public function run(): void
    {
        $entitlements = [
            ['name' => 'Event Access', 'description' => 'Access to community events', 'colour' => 'green'],
            ['name' => 'Gallery Upload', 'description' => 'Upload photos to gallery', 'colour' => 'blue'],
            ['name' => 'Story Creation', 'description' => 'Create and share stories', 'colour' => 'purple'],
            ['name' => 'News Publication', 'description' => 'Publish news articles', 'colour' => 'red'],
            ['name' => 'Article Writing', 'description' => 'Write and publish articles', 'colour' => 'orange'],
            ['name' => 'Comment Moderation', 'description' => 'Moderate user comments', 'colour' => 'yellow'],
            ['name' => 'User Management', 'description' => 'Manage user accounts', 'colour' => 'cyan'],
            ['name' => 'Admin Access', 'description' => 'Full administrative access', 'colour' => 'zinc'],
        ];

        foreach ($entitlements as $entitlement) {
            Entitlement::create($entitlement);
        }
    }
}
