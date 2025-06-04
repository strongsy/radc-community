<?php

namespace Database\Seeders;

use App\Models\Email;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Random\RandomException;

class ReplySeeder extends Seeder
{
    public function run(): void
    {
        // Get all emails and users
        $emails = Email::all();
        $users = User::all();

        // Create unique combinations of email_id and user_id
        $emails->each(/**
         * @throws RandomException
         */ function ($email) use ($users) {
            // Randomly select some users to reply to this email
            $randomUsers = $users->random(random_int(1, 3)); // Create 1-3 replies per email

            $randomUsers->each(function ($user) use ($email) {
                Reply::factory()->create([
                    'email_id' => $email->id,
                    'user_id' => $user->id,
                ]);
            });
        });

    }
}
