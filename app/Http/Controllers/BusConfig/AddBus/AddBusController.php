<?php

namespace App\Http\Controllers\BusConfig\AddBus;

use App\Http\Controllers\Controller;
use App\Models\BusConfig\AddBus\AddBus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AddBusController extends Controller
{
    public function addBus(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'operator_name' => 'required|string|max:255',
                'bus_name' => 'required|string|max:255',
                'bus_type' => 'required',
                'bus_plate_number' => 'required|string|max:20',
                'driver_name' => 'required|string|max:255',
                'driver_phone' => 'required|string|size:10',
                'driver_alternative_phone' => 'nullable|string|size:10',
            ]);

            if ($validator->fails()) {
                return response()->json(['validation_error' => $validator->messages()]);
            }

            $busData = AddBus::updateOrCreate(
                ['bus_plate_number' => $request->bus_plate_number],
                [
                    'operator_name' => $request->operator_name,
                    'bus_name' => $request->bus_name,
                    'bus_type' => $request->bus_type,
                    'driver_name' => $request->driver_name,
                    'driver_phone' => $request->driver_phone,
                    'driver_alternative_phone' => $request->driver_alternative_phone,
                ]
            );

            
            // Check if bus record is created
            if ($busData) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Bus added successfully',
                    'bus_data' => $busData,
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Failed to add bus, please check the form data',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ]);
        }
    }

}
