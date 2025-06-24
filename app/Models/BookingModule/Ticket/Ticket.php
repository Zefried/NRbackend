<?php

namespace App\Models\BookingModule\Ticket;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'operator_id',
        'date',
        'parent_route',
        'total_seats',
        'total_fair',
        'pnr',
    ];
}
