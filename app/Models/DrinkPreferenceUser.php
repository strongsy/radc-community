<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkPreferenceUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_session_user_id',
        'drink_preference_id',
    ];
}
