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
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'category_id',
        'cost_for_members',
        'cost_for_guests',
        'min_participants',
        'max_participants',
        'guests_allowed',
        'max_guests_per_user',
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'timestamp',
            'end_datetime' => 'timestamp',
            'guests_allowed' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function participants(): BelongsTo
    {
        return $this->belongsToMany(User::class, 'event_users', 'event_id', 'user_id')
            ->withPivot('status', 'guests_count')
            ->withTimestamps();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Gallery::class, 'imageable');
    }
}
