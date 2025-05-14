<?php

namespace App\Models\BusConfig\Amenities;

use Illuminate\Database\Eloquent\Model;


class Amenities extends Model
{
    protected $fillable = [
        'name',
        'is_disable',
    ];
}
