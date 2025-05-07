<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventGuest extends Model
{
    use HasFactory;

    protected $table = 'event_guest';

    protected $fillable = [
        'guest_id',
        'event_id',
    ];
}
