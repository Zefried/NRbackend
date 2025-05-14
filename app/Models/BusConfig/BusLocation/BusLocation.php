<?php

namespace App\Models\BusConfig\BusLocation;

use Illuminate\Database\Eloquent\Model;

class BusLocation extends Model
{
      // Specify the fields that are mass assignable
      protected $fillable = 
      [
        'location', 
        'short_code'
      ];
}
