<?php

namespace App\Http\Controllers\RouteModule\ServingRoutes;

use App\Http\Controllers\Controller;
use App\Models\RouteModule\ServingRoute\ServingRoute;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServingRouteController extends Controller
{
    public function resource(Request $request)
    {
        $type = $request->type;

        if ($type === 'store') {
            return $this->store($request);
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
            $data = $request->input('data');

            foreach ($data as $route) {
                $validator = Validator::make((array) $route, [
                    'operator_id' => 'required|integer',
                    'source' => 'required|string',
                    'destination' => 'required|string',
                    'parent_route' => 'required|string',
                    'direction' => 'required|in:forward,backward',
                    'time.from' => 'required|date_format:H:i:s',
                    'time.to' => 'required|date_format:H:i:s',
                    'unavailable_dates' => 'array',
                    'unavailable_dates.*' => 'date',
                    'state' => 'required|string',
                    'active_status' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'validation error',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                ServingRoute::updateOrCreate(
                    [
                        'operator_id' => $route['operator_id'],
                        'source' => $route['source'],
                        'destination' => $route['destination'],
                        'parent_route' => $route['parent_route'],
                        'direction' => $route['direction'],
                        'state' => $route['state'],
                    ],
                    [
                        'from' => $route['time']['from'],
                        'to' => $route['time']['to'],
                        'unavailable_dates' => $route['unavailable_dates'],
                        'active_status' => $route['active_status'],
                    ]
                );
            }

            return response()->json([
                'status' => 200,
                'message' => 'routes stored/updated successfully',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ], 500);
        }
    }
    





}
