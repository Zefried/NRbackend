<?php

namespace App\Models\BookingModule\SeatHold;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SeatHold extends Model
{
    protected $fillable = [
    'user_id',
    'seat_type',
    'seat_no',
    'operator_id',
    'date',
    'parent_route',
    'serving_route_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relations if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function operatorRef()
    // {
    //     return $this->belongsTo(test::class, 'operator_id');
    // }
}
