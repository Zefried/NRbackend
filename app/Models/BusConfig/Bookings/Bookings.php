<?php

namespace App\Models\BusConfig\Bookings;

use App\Models\BusConfig\AddBus\AddBus;
use App\Models\BusConfig\Orders\Orders;
use App\Models\BusConfig\RealTimeSeatHoldingStatus\RealTimeSeatHolding;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
  
    protected $fillable = [
        'user_id',
        'bus_id',
        'route_info_id',
        'pnr_code',
        'booking_status',
        'payment_status',
        'counter_no',
        'total_fare',
        'total_seats',
        'chalan_status',
        'chalan_no',
        'transaction_id',
        'origin',
        'destination',
        'date_of_journey',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function realTimeHoldings() {
        return $this->hasMany(RealTimeSeatHolding::class, 'booking_id');
    }

    public function bus()
    {
        return $this->belongsTo(AddBus::class, 'bus_id')->withDefault();
    }
}
