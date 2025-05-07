<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'detailable',
        'notes',
        'allergy_id',
        'food_id',
        'drink_id',
    ];
}
