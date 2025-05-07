<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gallery_id',
        'album_title',
        'album_desc',
        'cover_img',
    ];
}
