<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Entitlement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $user = User::factory()->create([
            'name' => 'Paul Armstrong',
            'email' => 'strongs@icloud.com',
            'password' => bcrypt('ginpalsup'),
            'email_verified_at' => now(),
            'community_id' => 3,
            'membership_id' => 1,
            'affiliation' => 'All files within the bucket are public and are publicly accessible via the Internet via a Laravel Cloud provided URL. These buckets are typically used for publicly viewable assets like user avatars.',
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => 1,
            'is_subscribed' => true,
            'is_blocked' => false,
            'unsubscribe_token' => Str::random(32),
        ]);
        $user->assignRole('super-admin');

        // Attach all entitlements to admin
        $user->entitlements()->attach(Entitlement::all());

        // Create regular users
        User::factory(50)->create()->each(function ($user) {
            // Randomly attach entitlements
            $entitlements = Entitlement::inRandomOrder()->limit(random_int(1, 4))->get();
            $user->entitlements()->attach($entitlements);
            $user->assignRole('user');
        });
    }
}
