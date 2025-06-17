<?php

namespace App\Http\Controllers\BusConfig\BusRouteInfo;

use App\Http\Controllers\Controller;
use App\Models\BusConfig\AddBus\AddBus;
use App\Models\BusConfig\BusRouteInfo\BusRouteInfoModel;
use App\Models\BusConfig\RealTimeSeatHoldingStatus\RealTimeSeatHolding;
use App\Models\BusConfig\SeatConfig\SeatConfig;
use Illuminate\Http\Request;

class BusRouteInfo extends Controller
{
    public function addRouteInfo(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => 'required|integer',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'rest_point' => 'nullable|string|max:255',
            'start_point' => 'nullable|array',
            'final_drop_point' => 'nullable|array',
            'rest_duration' => 'nullable|string|max:10',
            'routes' => 'nullable|array',
            'boarding_points' => 'nullable|array',
            'dropping_points' => 'nullable|array',
            'estimated_duration' => 'nullable|string|max:10',
            'distance_km' => 'nullable|numeric',
            'route_code' => 'nullable|string|max:100',
            'seater_base_price' => 'nullable|numeric',
            'sleeper_base_price' => 'nullable|numeric',
            'seater_discount' => 'nullable|numeric',
            'sleeper_discount' => 'nullable|numeric',
            'seater_offer_price' => 'nullable|numeric',
            'sleeper_offer_price' => 'nullable|numeric',
            'offline_dates' => 'nullable|array',
        ]);

        try {
            $data = $validated;

            $data['routes'] = isset($validated['routes']) ? json_encode($validated['routes']) : null;
            $data['boarding_points'] = isset($validated['boarding_points']) ? json_encode($validated['boarding_points']) : null;
            $data['dropping_points'] = isset($validated['dropping_points']) ? json_encode($validated['dropping_points']) : null;
            $data['offline_dates'] = isset($validated['offline_dates']) ? json_encode($validated['offline_dates']) : null;
            $data['start_point'] = isset($validated['start_point']) ? json_encode($validated['start_point']) : null;
            $data['final_drop_point'] = isset($validated['final_drop_point']) ? json_encode($validated['final_drop_point']) : null;
            
            $routeInfo = BusRouteInfoModel::updateOrCreate(
                ['bus_id' => $validated['bus_id']],
                $data
            );

            return response()->json(['status' => 200, 'message' => 'Route info saved', 'data' => $routeInfo]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to save route info', 'error' => $e->getMessage()], 500);
        }
    }

    public function searchBus(Request $request)
    {
        try {
        
        
            $busIds = BusRouteInfoModel::where('origin', $request->boarding)
                ->where('destination', $request->dropping)
                ->whereJsonDoesntContain('offline_dates', $request->date)
                ->pluck('bus_id');

            if ($busIds->isEmpty()) {
                
                return response()->json([
                    'status' => 202,
                    'message_status' => true,
                    'message' => 'Sorry! No Buses Found For Your Search...'
                ]);
            }

            $busRouteInfos = BusRouteInfoModel::whereIn('bus_id', $busIds)
                ->get(['bus_id', 'seater_offer_price', 'sleeper_offer_price', 'start_point', 'final_drop_point', 'estimated_duration']);

            $busDetails = AddBus::whereIn('id', $busIds)
                ->get(['id', 'bus_name', 'sleeper', 'Ac_type']);

            $seatConfigs = SeatConfig::whereIn('bus_id', $busIds)
                ->get(['bus_id', 'currently_avl', 'total_seats']);

            $result = [];
     
            foreach ($busIds as $busId) {
                $routeInfo = $busRouteInfos->firstWhere('bus_id', $busId);
                $busDetail = $busDetails->firstWhere('id', $busId);
                $seatConfig = $seatConfigs->firstWhere('bus_id', $busId);

                $result[] = [
                    'bus_id' => $busId,
                    'route_info' => $routeInfo,
                    'bus_detail' => $busDetail,
                    'seat_config' => $seatConfig,
                ];
            }

                return response()->json([
                    'data' => $result,
                    'redirect' => true,
                    'status' => 200
                ]);
         
         

        

        } catch (\Exception $e) {

            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch bus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchSingleBusData(Request $Request){
      
        $busId = $Request->input('bus_id');

        $busData = BusRouteInfoModel::where('bus_id', $busId)
        ->where('origin',$Request->origin)
        ->where('destination', $Request->destination)
        ->get([
            'routes',
            'boarding_points',
            'dropping_points',
            'rest_point',
            'rest_duration',
            'estimated_duration',
            'sleeper_offer_price',
            'seater_offer_price',
        ]);
        
        return response()->json($busData);
    }

    public function fetchBusState($busId)
    {
        try {
            $busState = AddBus::where('id', (int)$busId)->first(['sleeper', 'seater']);
            return response()->json([
                'status' => 200,
                'message' => 'Bus state fetched successfully',
                'data' => $busState
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch bus state',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function fetchBoardDropinfo(Request $request)
    {
        try {

            $routeInfoData = BusRouteInfoModel::where('bus_id', $request->bus_id)
                ->where('origin', $request->origin)
                ->where('destination', $request->destination)
                ->first(['boarding_points', 'dropping_points', 'id']);

            return response()->json([
                'status' => 200,
                'message' => 'Boarding & Dropping Points fetched successfully',
                'data' => $routeInfoData
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ]);

        }
    }


   


}
