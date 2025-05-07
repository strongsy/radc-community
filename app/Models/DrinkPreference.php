<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'drink_type',
    ];
}
