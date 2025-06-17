<?php

namespace App\Models\BusConfig\RealTimeSeatHoldingStatus;

use App\Models\BusConfig\Bookings\Bookings;
use Illuminate\Database\Eloquent\Model;

class RealTimeSeatHolding extends Model
{
   protected $fillable = [
    'bus_id',
    'seat_no',
    'seat_type',
    'user_id',
    'origin',
    'destination',
    'booking_id',
   ];

   public function bookings(){
      return $this->belongsTo(Bookings::class, 'booking_id');
   }
}
