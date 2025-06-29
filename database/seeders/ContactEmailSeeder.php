<?php

namespace Database\Seeders;

use App\Models\ContactEmail;
use Illuminate\Database\Seeder;

class ContactEmailSeeder extends Seeder
{
    public function run(): void
    {
        ContactEmail::factory(15)->create();
    }
}
