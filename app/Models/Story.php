<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_title',
        'story_content',
        'story_status',
        'story_category_id',
        'cover_img',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(StoryCategory::class, 'category_stories', 'story_id', 'story_category_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
