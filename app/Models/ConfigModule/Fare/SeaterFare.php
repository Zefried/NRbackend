<?php

namespace App\Models\ConfigModule\Fare;

use App\Models\RouteModule\ServingRoute\ServingRoute;
use Illuminate\Database\Eloquent\Model;

class SeaterFare extends Model
{
    protected $fillable = [
        'serving_route_id',
        'fare',
        'discount_flat',
        'discount_percent',
        'final_fare',
        'type',
    ];

    public function servingRoute()
    {
        return $this->belongsTo(ServingRoute::class);
    }
}
