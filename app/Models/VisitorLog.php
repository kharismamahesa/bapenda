<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $table = 'visitor_logs';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'browser',
        'city',
        'region',
        'country',
        'latitude',
        'longitude',
        'visited_date',
    ];

    protected $casts = [
        'visited_date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
