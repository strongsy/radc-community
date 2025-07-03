<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'county',
        'post_code',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function getFullAddress(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->county,
            $this->post_code,
        ]);

        return implode(', ', $addressParts);
    }

    public static function venueCounties(): array
    {
        return [
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
            'Anglesey',
            'Brecknockshire',
            'Caernarfonshire',
            'Cardiganshire',
            'Carmarthenshire',
            'Denbighshire',
            'Flintshire',
            'Glamorgan',
            'Merionethshire',
            'Monmouthshire',
            'Montgomeryshire',
            'Pembrokeshire',
            'Radnorshire',
            'Aberdeenshire',
            'Argyllshire and Buteshire',
            'Ayrshire',
            'Banffshire',
            'Berwickshire',
            'Caithness',
            'Dumbartonshire',
            'Dumfriesshire',
            'Antrim',
            'Armagh',
            'Down',
            'Fermanagh',
            'Derry',
            'Londonderry',
            'Tyrone',
        ];
    }
}
