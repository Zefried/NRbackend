<?php

namespace App\Models\BookingModule\Bookings\LayoutMaster;

use App\Models\BookingModule\Bookings\LayoutDetail\BookingLayoutDetail;
use Illuminate\Database\Eloquent\Model;

class BookingLayoutMaster extends Model
{
    protected $fillable = [
        'operator_id',
        'date',
        'parent_route',
        'master_key'
    ];

    public function bookingLayoutMaster(){
        return $this->hasMany(BookingLayoutDetail::class);
    }

}
