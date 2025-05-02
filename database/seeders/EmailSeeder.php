<?php

namespace Database\Seeders;

use App\Models\Email;
use Faker\Factory;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        // Create a variety of emails with different creation dates
        // Recent emails (last week)
        Email::factory(10)->create([
            'created_at' => $faker->dateTimeBetween('-1 week', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ]);

        // Older emails (1 week to 1 month ago)
        Email::factory(15)->create([
            'created_at' => $faker->dateTimeBetween('-1 month', '-1 week'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ]);

        // Create some emails with specific senders for testing
        $testEmails = [
            'john.doe@example.com',
            'jane.smith@example.com',
            'support@company.com',
            'notifications@service.org',
            'no-reply@system.net',
        ];

        foreach ($testEmails as $email) {
            Email::factory()->create([
                'sender_email' => $email,
                'sender_name' => explode('@', $email)[0],
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                'updated_at' => function (array $attributes) {
                    return $attributes['created_at'];
                },
            ]);
        }
    }
}
