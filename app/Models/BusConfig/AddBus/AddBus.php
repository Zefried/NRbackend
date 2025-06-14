<?php

namespace App\Models\BusConfig\AddBus;

use App\Models\BusConfig\Bookings\Bookings;
use App\Models\BusConfig\BusRouteInfo\BusRouteInfoModel;
use Illuminate\Database\Eloquent\Model;

class AddBus extends Model
{
    protected $fillable = [
        'unique_bus_id',
        'operator_name',
        'bus_name',
        'sleeper',
        'seater',
        'vip',
        'Ac_type',
        'bus_plate_number',
        'driver_name',
        'driver_phone',
        'driverTwo_name',
        'driverTwo_phone',
        'handyman_name',
        'handyman_phone',
        'bus_config',
    ];


    public function bookings(){
        return $this->hasMany(Bookings::class, 'bus_id');
    }

    public function allRoutes() {
        return $this->hasMany(BusRouteInfoModel::class);
    }
}
