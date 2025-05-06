<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\GalleryFactory;
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
            RoleAndPermissionSeeder::class, // Include in the array, not run manually
            UserSeeder::class,
        ]);
    }
}
