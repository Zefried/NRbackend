<?php

namespace App\Http\Controllers\BusConfig\Amenities;

use App\Models\BusConfig\Amenities\Amenities;
use Illuminate\Http\Request;
Use App\Http\Controllers\Controller;

class AmenitiesController extends Controller
{
    public function addAmenity(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $amenity = Amenities::create([
            'name' => $data['name'],
        ]);

        return response()->json($amenity, 201);
    }

}
