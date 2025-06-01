<?php

namespace App\Models\BusConfig\Bookings;

use App\Models\BusConfig\AddBus\AddBus;
use App\Models\BusConfig\Orders\Orders;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
  
    protected $fillable = [
        'user_id',
        'order_id',
        'bus_id',
        'transaction_id',
        'gender',
        'user_phone',
        'seat_type',
        'seat_no',
        'boarding',
        'dropping',
        'amount',
        'payment_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id')->withDefault();
    }

    public function bus()
    {
        return $this->belongsTo(AddBus::class, 'bus_id')->withDefault();
    }
}
