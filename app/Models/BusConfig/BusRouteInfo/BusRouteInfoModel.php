<?php

namespace App\Models\BusConfig\BusRouteInfo;

use App\Models\BusConfig\AddBus\AddBus;
use Illuminate\Database\Eloquent\Model;

class BusRouteInfoModel extends Model
{
    protected $fillable = [
        'bus_id',
        'origin',
        'destination',
        'rest_point',
        'rest_duration',
        'routes',
        'boarding_points',
        'dropping_points',
        'start_point',        // new JSON field
        'final_drop_point',   // new JSON field
        'estimated_duration',
        'distance_km',
        'route_code',
        'seater_base_price',
        'sleeper_base_price',
        'seater_discount',
        'sleeper_discount',
        'seater_offer_price',
        'sleeper_offer_price',
        'offline_dates'
    ];


    public function bus() {
        return $this->belongsTo(AddBus::class);
    }
}
