<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FoodAllergy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'colour',
    ];

    public function eventSessionUsers(): BelongsToMany
    {
        return $this->belongsToMany(EventSessionUser::class, 'food_allergy_users');
    }

    public function eventSessionGuests(): BelongsToMany
    {
        return $this->belongsToMany(EventSessionGuest::class, 'food_allergy_guests');
    }
}
