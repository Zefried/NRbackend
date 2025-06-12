<?php

namespace App\Models\BusConfig\RealTimeSeatHoldingStatus;

use Illuminate\Database\Eloquent\Model;

class RealTimeSeatHolding extends Model
{
   protected $fillable = [
    'bus_id',
    'seat_no',
    'seat_type',
    'user_id'
   ];
}
