<?php

namespace Database\Seeders;

use App\Models\EventSession;
use App\Models\EventSessionUser;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Random\RandomException;

class EventSessionSeeder extends Seeder
{
    public function run(): void
    {
        Event::all()->each(/**
         * @throws RandomException
         */ function ($event) {
            EventSession::factory(random_int(2, 5))->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
            ])->each(function ($session) {
                $users = User::inRandomOrder()->limit(random_int(5, 15))->get();

                // Use EventSessionUser model instead of direct relationship
                foreach ($users as $user) {
                    EventSessionUser::create([
                        'user_id' => $user->id,
                        'event_session_id' => $session->id,
                    ]);
                }
            });
        });
    }
}
