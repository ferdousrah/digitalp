<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'url',
        'source',
    ];
}
