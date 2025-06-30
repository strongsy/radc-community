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
            PermissionSeeder::class,
            CommunitySeeder::class,
            MembershipSeeder::class,
            EntitlementSeeder::class,
            CategorySeeder::class,
            TitleSeeder::class,
            VenueSeeder::class,
            FoodPreferenceSeeder::class,
            DrinkPreferenceSeeder::class,
            FoodAllergySeeder::class,
            UserSeeder::class,
            EventSeeder::class,
            EventSessionSeeder::class,
            ContentSeeder::class,
            ContactEmailSeeder::class,
        ]);
    }
}
