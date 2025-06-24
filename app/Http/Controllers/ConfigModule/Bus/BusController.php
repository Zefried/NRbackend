<?php

namespace App\Http\Controllers\ConfigModule\Bus;

use App\Http\Controllers\Controller;
use App\Models\ConfigModule\Bus\BusConfig;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusController extends Controller
{
    public function addBus(Request $request)
    {
        try {
            // Validation
                $validator = Validator::make($request->all(), [
                'operator_id' => 'required|integer',
                'bus_name' => 'required|string|max:255',
                'bus_plate_number' => 'required|string|max:20',
                'driver_name' => 'required|string|max:255',
                'driver_phone' => 'required|string|size:10',
                'driverTwo_name' => 'nullable|string|max:255',
                'driverTwo_phone' => 'nullable|string|size:10',
                'handyman_name' => 'nullable|string|max:255',
                'handyman_phone' => 'nullable|string|size:10',
                'ac_type' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['validation_error' => $validator->messages()], 422);
            }

            $payload = [
                'operator_id' => $request->operator_id,
                'bus_name' => $request->bus_name,
                'bus_plate_number' => $request->bus_plate_number,
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'driverTwo_name' => $request->driverTwo_name,
                'driverTwo_phone' => $request->driverTwo_phone,
                'handyman_name' => $request->handyman_name,
                'handyman_phone' => $request->handyman_phone,
                'ac_type' => $request->ac_type ?? true,
            ];

            $busData = BusConfig::where('bus_plate_number', $request->bus_plate_number)->first();

            if ($busData) {
                $busData->update($payload);
            } else {
                $payload['unique_bus_id'] = $this->generateUniqueId();
                $busData = BusConfig::create($payload);
            }


            if ($busData) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Bus added/updated successfully',
                    'bus_data' => $busData,
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Failed to add/update bus, please check the form data',
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


    private function generateUniqueId()
    {
        do {
            // Generate 6-character alphanumeric ID with 'BUS' prefix
            $uniqueId = 'BUS-' . strtoupper(substr(uniqid(), -4)) . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2)); 
        } while (BusConfig::where('unique_bus_id', $uniqueId)->exists()); // Check if ID already exists

        return $uniqueId;
    }
}
