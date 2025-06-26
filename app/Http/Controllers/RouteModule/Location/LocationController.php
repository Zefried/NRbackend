<?php

namespace App\Http\Controllers\RouteModule\Location;

use App\Http\Controllers\Controller;
use App\Models\RouteModule\Location\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function resource(Request $request)
    {
        $type = $request->type;

        if ($type === 'store') {
            return $this->store($request);
        }


        if ($type === 'search') {
            return $this->search($request);
        }


        return response()->json(['status' => false, 'message' => 'Invalid type'], 400);
    }

    private function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:store',
                'data' => 'required|array',
                'data.*.location' => 'required|string',
                'data.*.short_code' => 'required|string|max:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            foreach ($request->data as $item) {
                Location::create([
                    'location' => $item['location'],
                    'short_code' => $item['short_code']
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'locations stored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            $results = Location::where('location', 'like', "%{$query}%")
            ->orWhere('short_code', 'like', "%{$query}%")
            ->orWhereRaw("SOUNDEX(location) = SOUNDEX(?)", [$query])
            ->get();

            return response()->json([
                'status' => 200,
                'message' => 'Locations fetched successfully',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong while searching locations',
                'err' => $e->getMessage()
            ], 500);
        }
    }

   
}
