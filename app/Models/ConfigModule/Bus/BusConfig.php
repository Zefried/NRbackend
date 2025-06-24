<?php

namespace App\Models\ConfigModule\Bus;

use Illuminate\Database\Eloquent\Model;

class BusConfig extends Model
{
    // all buses are here
     protected $fillable = [
        'unique_bus_id',
        'operator_id',
        'bus_name',
        'Ac_type',
        'bus_plate_number',
        'driver_name',
        'driver_phone',
        'driverTwo_name',
        'driverTwo_phone',
        'handyman_name',
        'handyman_phone',
    ];

}
