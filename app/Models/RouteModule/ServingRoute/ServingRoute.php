<?php

namespace App\Models\RouteModule\ServingRoute;

use App\Models\ConfigModule\Boarding\Boarding;
use App\Models\ConfigModule\Fare\LowerBerthFare;
use App\Models\ConfigModule\Fare\SeaterFare;
use App\Models\ConfigModule\Fare\SleeperFare;
use App\Models\ConfigModule\Fare\UpperBerthFare;
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




    // Fare Related Relationships

    public function seaterFares()
    {
        return $this->hasMany(SeaterFare::class);
    }

    public function sleeperFares()
    {
        return $this->hasMany(SleeperFare::class);
    }

    public function upperBerthFares()
    {
        return $this->hasMany(UpperBerthFare::class);
    }

    public function lowerBerthFares()
    {
        return $this->hasMany(LowerBerthFare::class);
    }

    
}
