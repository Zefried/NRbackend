<?php

namespace App\Models\BusConfig\AddBus;

use Illuminate\Database\Eloquent\Model;

class AddBus extends Model
{
    protected $fillable = [
        'operator_name',
        'bus_name',
        'bus_type',
        'bus_plate_number',
        'driver_name',
        'driver_phone',
        'driver_alternative_phone',
        'bus_config',
    ];
}
