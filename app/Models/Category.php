<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'colour',
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'category_events');
    }

    public function stories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'category_stories');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'category_posts');
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'category_articles');
    }

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'category_news');
    }
}
