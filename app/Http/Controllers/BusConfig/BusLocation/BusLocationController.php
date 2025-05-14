<?php

namespace App\Http\Controllers\BusConfig\BusLocation;

use App\Http\Controllers\Controller;
use App\Models\BusConfig\BusLocation\BusLocation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusLocationController extends Controller
{
    
    // Data create starts here
    public function verifyLocation(Request $request)
    {
        try {
            // Get location short code from query parameters
            $locationShortCode = $request->query('location_shortCode');

            // Check if the short code is provided and has a length greater than 1
            if (strlen($locationShortCode) < 2) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Please enter at least 2 characters to search.'
                ]);
            }

            // Search for locations that match the short code (starts with the input)
            $locations = BusLocation::where('short_code', 'LIKE', $locationShortCode . '%')->get(['short_code']);

            if ($locations->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No matching location found.'
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Locations found.',
                'data' => $locations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while searching for locations.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function addLocation(Request $request)
    {
        try {
            // Validate input data using the private validation method
            $validationResult = $this->validateLocationData($request);

            if ($validationResult !== true) {
                return $validationResult;
            }
    
            // Create or update the BusLocation entry
            $location = BusLocation::updateOrCreate(
                ['short_code' => $request->location_shortCode], // Condition to find the record
                [
                    'location' => $request->location_name,  // The attributes to update or create
                    'short_code' => $request->location_shortCode,
                ]
            );
    
            // Return success response
            return response()->json([
                'status' => 201,
                'message' => 'Location added successfully.',
                'data' => $location
            ]);
    
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong on the server.',
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    // Private method for validation
    private function validateLocationData(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'location_shortCode' => 'required|string|max:50|regex:/^[a-zA-Z]*$/',
        ]);
    
        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed.',
                'errors' => $validate->messages()
            ]);
        }
    
        return true; // Return true if validation passes
    }
    // Data create ends here

    
    // Data View Starts here
    public function viewLocation(Request $request) {
        try {
            
            $limit = $request->query('limit'); // Default to 10 items per page
    
            $listData = BusLocation::paginate($limit);
    
            
            if ($listData->isNotEmpty()) {
                return response()->json([
                    'status' => 200,
                    'listData' => $listData,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No locations found.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong on the server.',
                'error' => $e->getMessage(),
            ]);
        }
    }
    // Data view Ends here

    
}
