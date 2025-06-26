<?php

namespace App\Http\Controllers\SearchModule;

use App\Http\Controllers\Controller;
use App\Models\ConfigModule\Boarding\Boarding;
use App\Models\ConfigModule\Company\CompanyInfo;
use App\Models\ConfigModule\Fare\SeaterFare;
use App\Models\RouteModule\ServingRoute\ServingRoute;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    public function searchBus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'source' => 'required|string',
                'destination' => 'required|string',
                'date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $routes = ServingRoute::where('source', $request->source)
                ->where('destination', $request->destination)
                ->where('active_status', 'online')
                ->get()
                ->filter(function ($route) use ($request) {
                    $dates = $route->unavailable_dates ?? [];
                    return !in_array($request->date, $dates);
                })
                ->values();
          
            $result = [];

            foreach ($routes as $route) {
                $busName = $this->getBusNameByOperator($route->operator_id);
                $acStatus = $this->getAcStatusByOperator($route->operator_id);
                $boardingInfo = $this->getBoardingInfo($route->id);
                $fare = $this->getFareByRoute($route->id);

                if ($busName) {
                    $result[] = [
                        'bus_name' => $busName,
                        'ac_status' => $acStatus,
                        'boarding_time' => $boardingInfo['boarding_time'],
                        'dropping_time' => $boardingInfo['dropping_time'],
                        'estimated_duration' => $boardingInfo['estimated_duration'],
                        'fare' => $fare,
                        'parent_route' => $route->parent_route,
                        'operator_id' => $route->operator_id,
                    ];
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Bus names fetched successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage(),
            ], 500);
        }
    }

    private function getBusNameByOperator($operatorId)
    {
        $company = CompanyInfo::where('operator_id', $operatorId)->first();
        return $company?->company_name;
    }

    private function getAcStatusByOperator($operatorId)
    {
        $company = CompanyInfo::where('operator_id', $operatorId)->first();
        return $company?->ac_status ?? false;
    }

    private function getBoardingInfo($routeId)
    {
        $boarding = Boarding::where('serving_route_id', $routeId)->first();

        return [
            'boarding_time' => $boarding?->boarding_time ?? null,
            'dropping_time' => $boarding?->dropping_time ?? null,
            'estimated_duration' => $boarding?->estimated_duration ?? null,
        ];
    }

    private function getFareByRoute($routeId)
    {
        $fare = SeaterFare::where('serving_route_id', $routeId)->first();

        return [
            'actual' => $fare?->fare ?? null,
            'discount_flat' => $fare?->discount_flat ?? null,
            'discount_percent' => $fare?->discount_percent ?? null,
            'final' => $fare?->final_fare ?? null,
        ];
    }
}
