<?php

namespace App\Models\BookingModule\Bookings\LayoutDetail;

use App\Models\BookingModule\Bookings\LayoutMaster\BookingLayoutMaster;
use Illuminate\Database\Eloquent\Model;

class BookingLayoutDetail extends Model
{
    protected $fillable = [
        'master_key_id',
        'seat_type',
        'available_seats',
        'total_seats',
        'double_seats',
        'female_booked',
        'available_for_female',
        'booked'
    ];

    public function bookingLayoutMaster(){
       return $this->belongsTo(BookingLayoutMaster::class)->withDefault();
    }

}
