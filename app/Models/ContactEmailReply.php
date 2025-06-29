<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactEmailReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_email_id',
        'subject',
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contactEmail(): BelongsTo
    {
        return $this->belongsTo(ContactEmail::class);
    }
}
