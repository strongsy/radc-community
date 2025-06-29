<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkPreferenceGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_session_guest_id',
        'drink_preference_id',
    ];
}
