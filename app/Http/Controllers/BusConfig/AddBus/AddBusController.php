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
                'driverTwo_name' => 'nullable|string|max:255',
                'driverTwo_phone' => 'nullable|string|size:10',
                'handyman_name' => 'nullable|string|max:255',
                'handyman_phone' => 'nullable|string|size:10',
                'sleeper' => 'nullable|boolean',
                'vip' => 'nullable|boolean',
                'Ac_type' => 'nullable|boolean',
                'bus_config' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['validation_error' => $validator->messages()]);
            }

            $busData = AddBus::where('bus_plate_number', $request->bus_plate_number)->first();

            $payload = [
                'operator_name' => $request->operator_name,
                'bus_name' => $request->bus_name,
                'bus_type' => $request->bus_type,
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'driver_alternative_phone' => $request->driver_alternative_phone,
                'driverTwo_name' => $request->driverTwo_name,
                'driverTwo_phone' => $request->driverTwo_phone,
                'handyman_name' => $request->handyman_name,
                'handyman_phone' => $request->handyman_phone,
                'sleeper' => $request->sleeper ?? false,
                'seater' => $request->seater ?? false,
                'vip' => $request->vip ?? false,
                'Ac_type' => $request->Ac_type ?? true,
                'bus_config' => $request->bus_config ?? 'pending',
            ];

            if ($busData) {
                $busData->update($payload);
            } else {
                $payload['bus_plate_number'] = $request->bus_plate_number;
                $payload['unique_bus_id'] = $this->generateUniqueId();
                $busData = AddBus::create($payload);
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
        } while (AddBus::where('unique_bus_id', $uniqueId)->exists()); // Check if ID already exists

        return $uniqueId;
    }



    
}
