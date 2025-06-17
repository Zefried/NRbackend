<?php

namespace App\Models\BusConfig\TicketModel;

use App\Models\BusConfig\PNR\PNRModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TicketModel extends Model
{
    protected $fillable = [
        'user_id',
        'bus_id',
        'route_info_id',
        'operator_name',
        'driver_name',
        'driver_number',
        'driver_two_name',
        'driver_two_number',
        'unique_ticket_no',
        'booking_id',
        'pnr_code',
        'origin',
        'destination',
        'date_of_journey',
        'payment_type',
        'counter_no',
        'reporting_time',
        'departure_time',
        'total_fare',
        'bus_type',
        'plate_no',
        'payment_status',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

}
