<?php

namespace App\Http\Controllers\BookingModule\SeatHold;

use App\Http\Controllers\Controller;
use App\Models\BookingModule\SeatHold\SeatHold;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatHoldController extends Controller
{
    public function resource(Request $request)
    {
        $type = $request->type;

        if ($type === 'storeRelease') {
            return $this->store($request);
        }

        if ($type == 'retrieve'){
            return $this->fetchHeldSeats($request);
        }

        // if ($type === 'view') {
        //     return $this->view($request);
        // }

        // if ($type === 'search') {
        //     return $this->search($request);
        // }

        // if ($type === 'update') {
        //     return $this->update($request);
        // }

        // if ($type === 'disable') {
        //     return $this->disable($request);
        // }

        // if ($type === 'delete') {
        //     return $this->delete($request);
        // }

        return response()->json(['status' => false, 'message' => 'Invalid type'], 400);
    }

    private function store($request)
    {   
        
        
         try {

            $item = $request->all();

            $validator = Validator::make($item, [
                'user_id' => 'required|integer',
                'seat_type' => 'required|string',
                'seat_no' => 'required|string',
                'operator_id' => 'required|integer',
                'parent_route' => 'required|string',
                'serving_route_id' => 'required|integer',
                'date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            SeatHold::where('operator_id', $request->operator_id)
            ->where('parent_route', $request->parent_route)
            ->where('date', $request->date)
            ->where('created_at', '<', now()->subMinutes(11))
            ->delete();

            $existing = SeatHold::where([
                'seat_type' => $item['seat_type'],
                'seat_no' => $item['seat_no'],
                'operator_id' => $item['operator_id'],
                'parent_route' => $item['parent_route'],
                'date' => $item['date'],
            ])->first();

                    if ($existing) {
                        if ($existing->user_id == $item['user_id']) {
                            $existing->delete(); // release by same user
                        } else {
                            return response()->json([
                                'status' => 403,
                                'message' => 'Seat already held by another user',
                            ]);
                        }
                    } else {
                        SeatHold::create($item); // store if not held
                    }

            return response()->json([
                'status' => 200,
                'message' => 'seat stored/released successfully',
                'date' => $existing,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage(),
            ], 500);
        }





    }

    public function fetchHeldSeats(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'operator_id' => 'required|integer',
                'date' => 'required|date',
                'parent_route' => 'required|string',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'validation error',
                    'errors' => $validated->errors(),
                ], 422);
            }

            SeatHold::where('operator_id', $request->operator_id)
            ->where('parent_route', $request->parent_route)
            ->where('date', $request->date)
            ->where('created_at', '<', now()->subMinutes(11))
            ->delete();

            $operatorId = $request->operator_id;
            $date = $request->date;
            $parentRoute = $request->parent_route;

            $seats = SeatHold::where('operator_id', $operatorId)
                ->where('date', $date)
                ->where('parent_route', $parentRoute)
                ->get(['seat_no', 'seat_type']);

            $grouped = [
                'seater' => $seats->where('seat_type', 'seater')->pluck('seat_no')->values(),
                'sleeper' => $seats->where('seat_type', 'sleeper')->pluck('seat_no')->values(),
                'upper' => $seats->where('seat_type', 'upper')->pluck('seat_no')->values(),
                'lower' => $seats->where('seat_type', 'lower')->pluck('seat_no')->values(),
            ];

            return response()->json([
                'status' => 200,
                'message' => 'seat hold records fetched successfully',
                'data' => $grouped,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage(),
            ], 500);
        }
    }


}
