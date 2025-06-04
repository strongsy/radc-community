<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_cat',
        'gallery_desc',
    ];

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }
}
