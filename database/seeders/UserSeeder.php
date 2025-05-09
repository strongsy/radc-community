<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Paul Armstrong',
            'email' => 'strongs@icloud.com',
            'password' => bcrypt('ginpalsup'),
            'email_verified_at' => now(),
            'community_id' => 3,
            'membership_id' => 1,
            'affiliation' => 'All files within the bucket are public and are publicly accessible via the Internet via a Laravel Cloud provided URL. These buckets are typically used for publicly viewable assets like user avatars.',
            'is_subscribed' => true,
            'is_active' => true,
            'is_blocked' => false,
            'unsubscribe_token' => Str::random(32),
        ]);
        $user->assignRole('super-admin');

        // Create regular users with the "user" role
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
