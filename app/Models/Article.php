<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_title',
        'article_content',
        'article_cat',
        'article_status',
        'cover_img',
    ];

    protected $casts = [
        'article_status' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function author(): BelongsTo
        {
           return $this->belongsTo(User::class, 'user_id');
        }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
