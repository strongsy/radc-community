<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // remainder of the model seeders
        $this->call([
            RoleAndPermissionSeeder::class,
            CommunitySeeder::class,
            EntitlementSeeder::class,
            MembershipSeeder::class,
            EmailSeeder::class,
            ReplySeeder::class,
            UserSeeder::class,
            EntitlementUserSeeder::class,
        ]);
    }
}
