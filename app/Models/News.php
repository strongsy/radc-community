<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'news_title',
        'news_content',
        'news_cat',
        'news_status',
        'release_at',
        'expires_at',
        'cover_img',
    ];

    protected function casts(): array
    {
        return [
            'release_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'category_news', 'news_id', 'news_category_id');
    }


    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
