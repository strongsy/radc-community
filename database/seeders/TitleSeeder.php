<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Seeder;

class TitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            ['name' => 'Southern Group Curry Club', 'description' => 'The southern group curry club is a group curry club located in the southeast part of the UK.'],
            ['name' => 'Northern Group Curry Club', 'description' => 'The northern group curry club is a group curry club located in the north of the UK.'],
            ['name' => 'Western Group Curry Club', 'description' => 'The western group curry club is a group curry club located in the west of the UK.'],
            ['name' => 'Eastern Group Curry Club', 'description' => 'The eastern group curry club is a group curry club located in the east of the UK.'],
            ['name' => 'Midlands Group Curry Club', 'description' => 'The midlands group curry club is a group curry club located in the midlands.'],
            ['name' => 'Scotland Group Curry Club', 'description' => 'The Scottish group curry club is a group curry club located in the Scotland.'],
            ['name' => 'NI Group Curry Club', 'description' => 'The Northern Ireland group curry club is a group curry club located in the Northern Ireland.'],
            ['name' => 'Wales Group Curry Club', 'description' => 'The Welsh group curry club is a group curry club located in the Wales.'],
            ['name' => 'RADC Officers Mess', 'description' => 'The RADC Officers Mess is normally held in Litchfield and is open to all serving officers of the RAMS Dental Branch and Former RADC Retired Officers.'],
            ['name' => 'RADC Past & Present WO\'s & Sgt\'s Mess', 'description' => 'The RADC Past & Present WO\'s & Sgt\'s Mess is normally held in Litchfield and is open to all veteran WO\'s & Sgt\'s of the former RADC.'],
        ];

        foreach ($titles as $title) {
            Title::create(
                ['name' => $title['name'],
                    'description' => $title['description'],
                ],
            );
        }

    }
}
