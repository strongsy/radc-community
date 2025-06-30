<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'location',
        'description',
        'start_date',
        'start_time',
        'end_time',
        'capacity',
        'allow_guests',
    ];

    protected $casts = [
        'start_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'allow_guests' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /*public function eventSessionTitle(): BelongsTo
    {
        return $this->belongsTo(EventSessionTitle::class);
    }

    public function eventSessionVenue(): BelongsTo
    {
        return $this->belongsTo(EventSessionVenue::class);
    }*/

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_session_users');
    }

    public function eventSessionUsers(): HasMany
    {
        return $this->hasMany(EventSessionUser::class);
    }

    public function eventSessionGuests(): HasMany
    {
        return $this->hasMany(EventSessionGuest::class);
    }
}
