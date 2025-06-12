<?php

namespace App\Http\Controllers;

use App\Models\BusConfig\BusLocation\BusLocation;
use App\Models\BusConfig\RealTimeSeatHoldingStatus\RealTimeSeatHolding;
use App\Models\BusConfig\SeatConfig\SeatConfig;
use Illuminate\Http\Request;

class TestController extends Controller
{

    public function findGender(request $request) {
        try {
           
        
            $seatNos = is_array($request->seat_no) ? $request->seat_no : [$request->seat_no];
      
            foreach ($seatNos as $seatNo) {

                $exists = RealTimeSeatHolding::where('bus_id', $request->bus_id)
                    ->where('seat_no', $seatNo)
                    ->where('seat_type', $request->seat_type)
                    ->exists();

                if ($exists) {

                    return response()->json([
                        'message' => "Seat $seatNo already on hold",
                        'status' => 'success',
                    ]);
    
                }

                RealTimeSeatHolding::create([
                    'bus_id' => $request->bus_id,
                    'seat_type' => $request->seat_type,
                    'seat_no' => $seatNo,
                ]);
                
            }

            return response()->json([

                'message' => 'Seat(s) held successfully',
                'status' => 'success'

            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function searchLocation(Request $request)
    {
        try {
            $query = $request->input('query');
    
            $results = BusLocation::where('location', 'like', "%{$query}%")
            ->orWhere('short_code', 'like', "%{$query}%")
            ->get();
                
            return response()->json([
                'status' => 200,
                'message' => 'Locations fetched successfully',
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong while searching locations',
                'error' => $e->getMessage()
            ]);
        }
    }


}
