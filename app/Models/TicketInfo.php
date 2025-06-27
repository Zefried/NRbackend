<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketInfo extends Model
{
    protected $fillable = [
        'pnr', 'booking_id', 'total_fare', 'base_fare', 'taxes', 'user_id', 'name', 'gender',
        'parent_route', 'operator_id', 'date_of_journey', 'source', 'destination',
        'boarding_point', 'dropping_point', 'duration', 'passengers', 'payment_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
