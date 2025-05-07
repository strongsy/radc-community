<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_type',
    ];
}
