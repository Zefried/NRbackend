<?php

namespace App\Models\BusConfig\Orders;

use App\Models\BusConfig\Bookings\Bookings;
use App\Models\BusConfig\SeatHoldingConfig\SeatHoldingConfig;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'bus_id',
        'bus_name_plate',
        'user_id',
        'user_phone',
        'payment_status',
        'customer_name',
        'gender',
        'age',
        'boarding',
        'dropping',
        'amount',
        'order_status',
    ];

    public function booking()
    {
        return $this->hasOne(Bookings::class, 'order_id')->withDefault();;
    }

    public function orderSeatConfig()
    {
        return $this->hasMany(SeatHoldingConfig::class, 'order_id');
    }

}
