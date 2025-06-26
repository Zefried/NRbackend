<?php

namespace App\Http\Controllers\SearchModule;

use App\Http\Controllers\Controller;
use App\Models\BookingModule\Bookings\LayoutDetail\BookingLayoutDetail;
use App\Models\BookingModule\Bookings\LayoutMaster\BookingLayoutMaster;
use App\Models\ConfigModule\Layouts\Standard\StandardLayDetail\StandardLayDetail;
use App\Models\ConfigModule\Layouts\Standard\StandardLayMaster\StandardLayMaster;
use Exception;
use Illuminate\Http\Request;

class ClientSearchController extends Controller
{
    public function fetchSeatUI(Request $request)
    {
        try {
            $request->validate([
                'operator_id' => 'required|integer',
                'seat_type' => 'required|string|in:seater,sleeper,double_sleeper',
            ]);

            $layoutMaster = StandardLayMaster::where('operator_id', $request->operator_id)
                ->where($request->seat_type, true) // dynamic column: seater/sleeper/double_sleeper
                ->first();

            if (!$layoutMaster) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No layout master found for this operator and seat type',
                ]);
            }

            $layoutDetails = StandardLayDetail::where('layout_id', $layoutMaster->id)
                ->where('type', $request->seat_type) // 'type' holds seat_type info
                ->get();

            return response()->json([
                'status' => 200,
                'message' => 'Layout data fetched successfully',
                'data' => $layoutDetails,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ]);
        }
    }

    public function fetchSeatData(Request $request)
    {
        try {
            $data = BookingLayoutMaster::where('date', $request->date)
                ->where('operator_id', $request->operator_id)
                ->where('parent_route', $request->parent_route)
                ->first();

            if (!$data) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No master layout found for given inputs.'
                ]);
            }

            $masterKey = $data->master_key;

            $bookingLayoutDetail = BookingLayoutDetail::where('master_key_id', $masterKey)
                ->where('seat_type', $request->seat_type)
                ->first(['available_for_female', 'female_booked', 'booked']);

            return response()->json([
                'status' => 200,
                'message' => 'seat data fetched',
                'data' => $bookingLayoutDetail
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ]);
        }
    }

}
