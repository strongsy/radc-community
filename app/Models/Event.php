<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title_id',
        'event_content',
        'event_date',
        'event_time',
        'venue_id',
        'category_id',
        'event_status',
        'allow_guests',
        'max_guests',
        'max_attendees',
        'user_cost',
        'guest_cost',
        'cover_img',
        'closes_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'event_time' => 'datetime:H:i:s',
            'allow_guests' => 'boolean',
            'closes_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(EventTitle::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): belongsToMany
    {
        return $this->belongsToMany(EventCategory::class, 'category_event', 'event_id', 'event_category_id');
    }

    public function venue(): belongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withTimestamps();
    }

    public function guests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_guest')
            ->withTimestamps();
    }


    /**
     * Scope a query to clean expired entries by setting the 'deleted_at' timestamp.
     */
    public function scopeCleanExpired($query): void
    {
        $query->where('expires_at', '<=', now())
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

    }
}
