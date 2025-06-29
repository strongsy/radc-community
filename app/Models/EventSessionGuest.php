<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EventSessionGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_session_id',
        'name',
        'email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventSession(): BelongsTo
    {
        return $this->belongsTo(EventSession::class);
    }

    public function foodPreferences(): BelongsToMany
    {
        return $this->belongsToMany(FoodPreference::class, 'food_preference_guests');
    }

    public function drinkPreferences(): BelongsToMany
    {
        return $this->belongsToMany(DrinkPreference::class, 'drink_preference_guests');
    }

    public function foodAllergies(): BelongsToMany
    {
        return $this->belongsToMany(FoodAllergy::class, 'food_allergy_guests');
    }
}
