<?php

namespace App\Models\BusConfig\SeatHoldingConfig;

use App\Models\BusConfig\Orders\Orders;
use Illuminate\Database\Eloquent\Model;

class SeatHoldingConfig extends Model
{
    protected $fillable = [
        'order_id', 
        'bus_id',
        'user_id',
        'seat_no_hold',
        'seat_type',
        'holding_disable',
    ];



    public function orders(){
        return $this->belongsTo(Orders::class)->withDefault();
    }
}

