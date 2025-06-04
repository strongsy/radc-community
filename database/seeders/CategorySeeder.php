<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use App\Models\EventCategory;
use App\Models\NewsCategory;
use App\Models\StoryCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
       EventCategory::insert([
           ['name' => 'Social'],
           ['name' => 'Food'],
           ['name' => 'Drink'],
           ['name' => 'CPD'],
           ['name' => 'Cenotaph'],
           ['name' => 'Ceremony'],
           ['name' => 'Conference'],
           ['name' => 'Lecture'],
           ['name' => 'Meeting'],
           ['name' => 'Professional'],
           ['name' => 'Turning of the Page'],
           ['name' => 'Adventure Training'],
           ['name' => 'Officers Mess'],
           ['name' => 'RADC Officers Mess'],
           ['name' => 'RADC WO & Sgt Mess'],
           ['name' => 'Other'],
       ]);

       NewsCategory::insert([
           ['name' => 'General'],
           ['name' => 'Community Update'],
           ['name' => 'Events Coverage'],
           ['name' => 'Members News'],
           ['name' => 'Announcements'],
           ['name' => 'Press Release'],
           ['name' => 'Breaking'],
           ['name' => 'Death'],
           ['name' => 'Marriage'],
           ['name' => 'Birth'],
           ['name' => 'Other'],
       ]);

       StoryCategory::insert([
           ['name' => 'Reserves'],
           ['name' => 'Veterans'],
           ['name' => 'Civilians'],
           ['name' => 'Serving'],
           ['name' => 'Inspirational'],
           ['name' => 'Testimonial'],
           ['name' => 'Personal'],
           ['name' => 'Service'],
           ['name' => 'National Service'],
           ['name' => 'Other'],
       ]);

       ArticleCategory::insert([
           ['name' => 'General'],
           ['name' => 'News'],
           ['name' => 'Research'],
           ['name' => 'Opinion'],
           ['name' => 'Study'],
           ['name' => 'RADC'],
           ['name' => 'Dental Hygiene'],
           ['name' => 'Dental Technician'],
           ['name' => 'Dentist'],
           ['name' => 'Dental Clerk Assistant'],
           ['name' => 'Other'],
       ]);
    }
}
