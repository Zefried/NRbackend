<?php

namespace App\Models\BusConfig\VipConfig;

use Illuminate\Database\Eloquent\Model;

class VipConfig extends Model
{
    protected $fillable=[
        'bus_id',
        'seat_row',
        'layout',
        'total_seats',
        'currently_avl',
        'booked',
        'blocked_real_time',
    ];
}
