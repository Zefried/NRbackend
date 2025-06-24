<?php

namespace App\Models\ConfigModule\Boarding;

use App\Models\RouteModule\ServingRoute\ServingRoute;
use Illuminate\Database\Eloquent\Model;

class Boarding extends Model
{
   protected $fillable = [
        'serving_route_id',
        'boarding_point',
        'dropping_point',
        'boarding_time',
        'dropping_time',
        'estimated_duration',
        'arrival_at',
        'delayed'
    ];

    public function servingRoute()
    {
        return $this->belongsTo(ServingRoute::class);
    }
}
