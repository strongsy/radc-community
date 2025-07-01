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

}
