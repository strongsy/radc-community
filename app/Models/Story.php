<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_title',
        'story_content',
        'story_status',
        'story_cat',
        'cover_img',
    ];

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
