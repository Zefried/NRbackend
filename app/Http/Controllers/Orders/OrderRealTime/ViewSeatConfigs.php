<?php

namespace App\Http\Controllers\Orders\OrderRealTime;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusConfig\RealTimeSeatHoldingStatus\RealTimeSeatHolding;
use Exception;

class ViewSeatConfigs extends Controller
{
    public function viewSeatConfigs(request $request){
        try{
            
            $expiredTime = now()->subMinutes(10);
            RealTimeSeatHolding::where('created_at', '<', $expiredTime)->delete();

            $seatsOnHold = RealTimeSeatHolding::where('bus_id', $request->bus_id)
            ->where('seat_type', $request->seat_type)
            ->get(['seat_no', 'user_id']);


            return response()->json([
            'status' => 200,
            'message' => 'Seats fetched successfully',
            'seatsOnHold' => $seatsOnHold
            ]);

            
        }catch(Exception $e){
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong, please try again",
                'error' => $e->getMessage(),
            ]);
        }
    }
}
