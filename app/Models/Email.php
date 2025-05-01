<?php

namespace App\Models;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model implements shouldQueue
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_name',
        'sender_email',
        'subject',
        'message',
    ];
}
