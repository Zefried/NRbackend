<?php

namespace App\Models\BusConfig\PNR;

use Illuminate\Database\Eloquent\Model;

class PNRModel extends Model
{
    protected $fillable = [
        'booking_id',
        'pnr_code',
        'seat_type',
        'seat_no',
        'name',
        'gender',
        'age',
    ];
}
