<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Random\RandomException;

class FollowSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $users = User::all();
        $follows = [];
        $existingPairs = [];

        foreach ($users as $follower) {
            // Choose 2–5 random *other* users to follow
            $following = $users->where('id', '!=', $follower->id)->random(random_int(2, 5))->pluck('id');

            foreach ($following as $followedId) {
                $pairKey = $follower->id.'-'.$followedId;

                if (! isset($existingPairs[$pairKey])) {
                    $follows[] = [
                        'follower_id' => $follower->id,
                        'followed_id' => $followedId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $existingPairs[$pairKey] = true; // Track to prevent duplicates
                }
            }
        }

        DB::table('follows')->insert($follows);
    }
}
