<?php

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $memberships = [
            ['name' => 'Bronze Member', 'description' => 'Basic membership with standard benefits', 'colour' => 'amber'],
            ['name' => 'Silver Member', 'description' => 'Enhanced membership with additional benefits', 'colour' => 'zinc'],
            ['name' => 'Gold Member', 'description' => 'Premium membership with exclusive benefits', 'colour' => 'yellow'],
            ['name' => 'Platinum Member', 'description' => 'Elite membership with all benefits', 'colour' => 'cyan'],
            ['name' => 'Life Member', 'description' => 'Lifetime membership with permanent benefits', 'colour' => 'green'],
            ['name' => 'Honorary Member', 'description' => 'Special recognition membership', 'colour' => 'purple'],
            ['name' => 'Associate Member', 'description' => 'Associate level membership', 'colour' => 'blue'],
            ['name' => 'Family Member', 'description' => 'Family of veteran membership', 'colour' => 'pink'],
        ];

        foreach ($memberships as $membership) {
            Membership::create($membership);
        }
    }
}
