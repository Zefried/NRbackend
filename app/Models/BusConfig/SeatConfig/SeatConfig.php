<?php

namespace App\Models\BusConfig\SeatConfig;

use Illuminate\Database\Eloquent\Model;

class SeatConfig extends Model
{
    protected $fillable=[
        'bus_id',
        'seat_row',
        'layout',
        'user_seat_type',
        'user_seat_no',
        'total_seats',
        'currently_avl',
        'booked',
        'double_side',
        'booked_by_female',
        'booked_by_other',
        'blocked_for_male',
        'blocked_real_time',
    ];
}
