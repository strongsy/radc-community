<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'story_category_id',
    ];
}
