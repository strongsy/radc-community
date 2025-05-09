<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'event_title',
        'event_content',
        'event_date',
        'event_time',
        'event_loc',
        'event_cat',
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

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'event_cat');
    }

}
