<?php

namespace App\Models\RouteModule\ServingRoute;

use App\Models\ConfigModule\Boarding\Boarding;
use Illuminate\Database\Eloquent\Model;

class ServingRoute extends Model
{
    protected $fillable = [
        'operator_id',
        'source',
        'destination',
        'parent_route',
        'direction',
        'from',
        'to',
        'unavailable_dates',
        'state',
        'active_status',
    ];

    protected $casts = [
        'unavailable_dates' => 'array',
        'from' => 'datetime:H:i:s',
        'to' => 'datetime:H:i:s',
    ];

    public function boardingDetails()
    {
        return $this->hasMany(Boarding::class);
    }

    
}
