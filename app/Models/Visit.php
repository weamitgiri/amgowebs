<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'country', 'country_name', 'path', 'referrer_host', 'device', 'visitor_hash', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];
}
