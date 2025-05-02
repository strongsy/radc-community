<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create an instance of the RoleAndPermissionSeeder class
        $roleAndPermissionSeeder = new RoleAndPermissionSeeder;
        $roleAndPermissionSeeder->run(); // Call the run method on the instance
        // Then emails
        $this->call(EmailSeeder::class);

        // Then replies (which depend on emails)
        $this->call(ReplySeeder::class);

        /**
         * create a super admin user
         */
        $user = User::factory()->create([
            'name' => 'Paul Armstrong',
            'email' => 'strongs@icloud.com',
            'password' => bcrypt('ginpalsup'),
            'community' => 'Veteran',
            'membership' => 'Life',
            'affiliation' => 'All files within the bucket are public and are publicly accessible via the Internet via a Laravel Cloud provided URL. These buckets are typically used for publicly viewable assets like user avatars.',
            'is_subscribed' => true,
            'is_active' => true,
            'is_blocked' => false,
            'unsubscribe_token' => Str::random(32),
        ]);

        $user->assignRole('super-admin');

        // Create users
        $users = User::factory(100)->create();

        foreach ($users as $user) {
            $user->assignRole('user');
        }
    }
}
