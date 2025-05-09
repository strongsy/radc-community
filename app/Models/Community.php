<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @method static updateOrCreate(array $array, array $community)
 * @method static pluck(string $string)
 */
class Community extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
