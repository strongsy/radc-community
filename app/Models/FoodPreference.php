<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FoodPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'colour',
    ];

    public function eventSessionUsers(): BelongsToMany
    {
        return $this->belongsToMany(EventSessionUser::class, 'food_preference_users');
    }

    public function eventSessionGuests(): BelongsToMany
    {
        return $this->belongsToMany(EventSessionGuest::class, 'food_preference_guests');
    }
}
