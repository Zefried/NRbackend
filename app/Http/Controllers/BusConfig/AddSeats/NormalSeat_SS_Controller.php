<?php

namespace App\Http\Controllers\BusConfig\AddSeats;

use App\Http\Controllers\Controller;
use App\Models\BusConfig\SeatConfig\SeatConfig;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NormalSeat_SS_Controller extends Controller
{

    public function addSeat(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'seat_row' => 'required|integer',
                'layout' => 'required|integer',
                'total_seats' => 'required|integer'
            ]);

            if($validator->fails()){
                return response()->json(['validation_error' => $validator->getMessageBag()]);
            }

            if($request->seat_type === 'vip'){

                
            } else if($request->seat_type === 'sleeper'){
                // for sleeper
            } else {

                $data = SeatConfig::create([
                    'bus_id' => $request->bus_id,
                    'seat_row' => $request->seat_row,
                    'layout' => $request->layout,
                    'total_seats' => $request->total_seats
                ]);

                if ($data->total_seats) {
                $data->double_side = $this->handleDoubleSeatSide($data->total_seats);
                $data->save();
            }

                return response()->json([
                    'status' => 200,
                    'message' => 'Seat data stored successfully',
                    'seatConfig' => $data
                ]);

            }

        } catch(Exception $e){
            return response()->json([
                'status' => 500,
                'message' => 'server catch error',
                'error' => $e->getMessage(),
            ]);
        }
    }


    private function handleDoubleSeatSide($totalSeats){
        try {
            $doubleSide = [];

            for ($i = 2; $i < $totalSeats; $i += 3) {
                if ($i + 1 <= $totalSeats) {
                    $doubleSide[] = [$i, $i + 1];
                }
            }

            return json_encode($doubleSide);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'server catch error',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function returnDoubleSeatSide(Request $request) {
         
        $doubleSides = SeatConfig::get(['double_side']);

        return response()->json($doubleSides);
    
        $seatMap = [];

        foreach ($doubleSides as [$left, $right]) {
            $seatMap[$left] = $right;
            $seatMap[$right] = $left;
        }

        $seat = (int) $request->seat;
        $adjacent = $seatMap[$seat] ?? false;

        return response()->json(['adjacent' => $adjacent]);

    }

    

}
