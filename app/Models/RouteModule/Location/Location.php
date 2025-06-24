<?php

namespace App\Models\RouteModule\Location;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = 
    [
        'location', 
        'short_code'
    ];
}
