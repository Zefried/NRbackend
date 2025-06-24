<?php

namespace App\Http\Controllers\ConfigModule\Boarding;

use App\Http\Controllers\Controller;
use App\Models\ConfigModule\Boarding\Boarding;
use App\Models\RouteModule\ServingRoute\ServingRoute;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoardingController extends Controller
{
    
    public function resource(Request $request)
    {
        $type = $request->type;

        if ($type === 'store') {
            return $this->store($request);
        }

        if($type == 'retrieve'){
            return $this->fetch($request);
        }

        return response()->json(['status' => false, 'message' => 'Invalid type'], 400);
    }

    private function store($request)
    {
        try {
            $data = $request->input('data');
            $this->handleTimeCal($data);

            return response()->json([
                'status' => 200,
                'message' => 'boarding details stored/updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ], 500);
        }
    }

    public function fetch($request)
    {
        try {
            $id = $request->id;

            if (!$id) {
                return response()->json([
                    'status' => 422,
                    'message' => 'ID is required'
                ], 422);
            }

            $route = ServingRoute::with('boardingDetails')->find($id);

            if (!$route) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Serving route not found'
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'serving route with boarding details retrieved',
                'data' => $route
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ], 500);
        }
    }





    /// helper functions

    private function handleTimeCal(array $data)
    {
        foreach ($data as $item) {

            $validator = Validator::make((array) $item, [
                'serving_route_id' => 'required|exists:serving_routes,id',
                'boarding_point' => 'nullable|string',
                'dropping_point' => 'nullable|string',
                'boarding_time' => 'nullable|date_format:H:i:s',
                'dropping_time' => 'nullable|date_format:H:i:s',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'validation_error' => $validator->errors(),
                ]);
            }

            $boardingTime = Carbon::createFromFormat('H:i:s', $item['boarding_time']);
            $droppingTime = Carbon::createFromFormat('H:i:s', $item['dropping_time']);

            $estimatedDuration = $boardingTime->diffInMinutes($droppingTime, false);
            $arrivalAt = $boardingTime->copy()->addMinutes($estimatedDuration);

            $existing = Boarding::where([
                'serving_route_id' => $item['serving_route_id'],
                'boarding_point' => $item['boarding_point'],
                'dropping_point' => $item['dropping_point'],
            ])->first();

            $delay = 0;
            if ($existing && $existing->dropping_time) {
                $oldDrop = Carbon::createFromFormat('H:i:s', $existing->dropping_time);
                $diff = $oldDrop->diffInMinutes($droppingTime, false);
                $delay = $diff > 0 ? $diff : 0;
            }

            Boarding::updateOrCreate(
                [
                    'serving_route_id' => $item['serving_route_id'],
                    'boarding_point' => $item['boarding_point'],
                    'dropping_point' => $item['dropping_point'],
                ],
                [
                    'boarding_time' => $item['boarding_time'],
                    'dropping_time' => $item['dropping_time'],
                    'estimated_duration' => $estimatedDuration + $delay,
                    'arrival_at' => $arrivalAt->addMinutes($delay)->format('H:i:s'),
                    'delayed' => $delay,
                ]
            );
        }
    }





}
