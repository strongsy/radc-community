<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        $ukCounties = [
            'Bedfordshire',
            'Berkshire',
            'Bristol',
            'Buckinghamshire',
            'Cambridgeshire',
            'Cheshire',
            'Cornwall',
            'Cumbria',
            'Derbyshire',
            'Devon',
            'Dorset',
            'Durham',
            'East Sussex',
            'Essex',
            'Gloucestershire',
            'Greater London',
            'Hampshire',
            'Hertfordshire',
            'Kent',
            'Lancashire',
            'Leicestershire',
            'Lincolnshire',
            'Norfolk',
            'Northamptonshire',
            'Northumberland',
            'Nottinghamshire',
            'Oxfordshire',
            'Somerset',
            'Suffolk',
            'Surrey',
            'West Sussex',
            'Yorkshire',
        ];

        return [
            'name' => $this->faker->randomElement([
                    'Veterans Hall',
                    'Community Center',
                    'Memorial Park',
                    'Legion Hall',
                    'VFW Post',
                    'Town Hall',
                    'Conference Center',
                    'Hotel Ballroom',
                    'Church Hall',
                    'School Auditorium'
                ]) . ' - ' . $this->faker->city(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'county' => $this->faker->randomElement($ukCounties),
            'post_code' => $this->faker->postcode(),
        ];
    }
}
