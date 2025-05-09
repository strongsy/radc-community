<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
