<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LaravelIdea\Helper\App\Models\_IH_Comment_QB;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_id',
        'commentable_type',
        'parent_id',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    // Parent comment relationship (for replies)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    // Child comments (replies) relationship
    public function replies(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id')->with('user', 'replies');
    }

    // Top-level comments only
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function dislikes(): MorphMany
    {
        return $this->morphMany(Dislike::class, 'dislikeable');
    }

    // Helper methods for likes/dislikes
    public function likesCount(): int
    {
        return $this->likes()->count();
    }

    public function dislikesCount(): int
    {
        return $this->dislikes()->count();
    }

    public function isLikedBy($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isDislikedBy($userId): bool
    {
        return $this->dislikes()->where('user_id', $userId)->exists();
    }

}
