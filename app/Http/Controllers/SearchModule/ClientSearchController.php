<?php

namespace App\Http\Controllers\SearchModule;

use App\Http\Controllers\Controller;
use App\Models\BookingModule\Bookings\LayoutDetail\BookingLayoutDetail;
use App\Models\BookingModule\Bookings\LayoutMaster\BookingLayoutMaster;
use App\Models\BookingModule\PNR\Detail\PnrDetail;
use App\Models\BookingModule\PNR\Master\PnrMaster;
use App\Models\BookingModule\SeatHold\SeatHold;
use App\Models\ConfigModule\Boarding\Boarding;
use App\Models\ConfigModule\Fare\SeaterFare;
use App\Models\ConfigModule\Fare\SleeperFare;
use App\Models\ConfigModule\Layouts\Standard\StandardLayDetail\StandardLayDetail;
use App\Models\ConfigModule\Layouts\Standard\StandardLayMaster\StandardLayMaster;
use App\Models\RouteModule\ServingRoute\ServingRoute;
use App\Models\TicketInfo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function fetchBoardingDetail(Request $request)
    {
        try {
            $servingRouteId = ServingRoute::where('source', $request->source)
                ->where('destination', $request->destination)
                ->where('operator_id', $request->operator_id)
                ->where('parent_route', $request->parent_route)
                ->where('unavailable_dates', '!=', $request->date)
                ->pluck('id');

            $data = Boarding::whereIn('serving_route_id', $servingRouteId)->get();

            return response()->json([
                'status' => 200,
                'message' => 'boarding points fetched successfully',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage(),
            ]);
        }
    }

    public function getUserSeatHoldings(Request $request)
    {
        try {
            // Delete expired seat holds
            SeatHold::where('operator_id', $request->operator_id)
                ->where('parent_route', $request->parent_route)
                ->where('date', $request->date_of_journey)
                ->where('created_at', '<', now()->subMinutes(11))
                ->delete();

            // Get active seat holds for user
            $seatData = SeatHold::where('user_id', $request->user_id)
                ->where('operator_id', $request->operator_id)
                ->where('date', $request->date)
                ->where('parent_route', $request->parent_route)
                ->get(['user_id', 'seat_type', 'seat_no']);

            // Fetch serving route ids
            $servingRouteIds = ServingRoute::where('operator_id', $request->operator_id)
                ->where('parent_route', $request->parent_route)
                ->where('source', $request->source)
                ->where('destination', $request->destination)
                ->where('unavailable_dates', '!=', $request->date_of_journey)
                ->pluck('id');

            // Prepare result
            $result = [];
            foreach ($seatData as $item) {
                switch ($item->seat_type) {
                    case 'seater':
                        $result[] = SeaterFare::whereIn('serving_route_id', $servingRouteIds)->first();
                        break;
                    case 'sleeper':
                        $result[] = SleeperFare::whereIn('serving_route_id', $servingRouteIds)->first();
                        break;
                    case 'upper':
                        $result[] = 'upper';
                        break;
                    case 'lower':
                        $result[] = 'lower';
                        break;
                    default:
                        $result[] = 'could not find type';
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Seat holdings fetched successfully',
                'data' => [$result, $seatData]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ]);
        }
    }

    public function getUserPnr(Request $request){
            try {
                $pnr = PnrMaster::where('operator_id', $request->operator_id)
                    ->where('date', $request->date)
                    ->where('user_id', $request->user_id)
                    ->where('parent_route', $request->parent_route)
                    ->pluck('pnr');

                $passengerData = PnrDetail::whereIn('pnr', $pnr)->get();

                return response()->json([
                    'status' => 200,
                    'message' => 'User PNR data fetched successfully',
                    'data' => $passengerData
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => 'something went wrong in server',
                    'err' => $e->getMessage()
                ]);
            }
    }

    public function generateUserTicket(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'name' => 'required|string',
                'gender' => 'nullable|string',
                'operator_id' => 'required|integer',
                'origin' => 'required|string',
                'destination' => 'required|string',
                'parent_route' => 'required|string',
                'date_of_journey' => 'required|date',
                'pnr' => 'nullable|string',
                'booking_id' => 'nullable|string', // changed to string
                'total_fare' => 'nullable|numeric',
                'base_fare' => 'nullable|numeric',
                'taxes' => 'nullable|numeric',
                'boarding_point.location' => 'required|string',
                'boarding_point.time' => 'required|string',
                'dropping_point.location' => 'required|string',
                'dropping_point.time' => 'required|string',
                'duration' => 'nullable|string',
                'passengers' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ]);
            }

            $data = $request->only([
                'pnr', 'booking_id', 'total_fare', 'base_fare', 'taxes', 'user_id', 'name', 'gender',
                'parent_route', 'operator_id', 'date_of_journey', 'origin', 'destination', 'duration', 'passengers'
            ]);

            // Flatten nested boarding and dropping data
            $data['boarding_point'] = $request->input('boarding_point.location') . ' (' . $request->input('boarding_point.time') . ')';
            $data['dropping_point'] = $request->input('dropping_point.location') . ' (' . $request->input('dropping_point.time') . ')';
            $data['pnr'] = $request->passengers[0]['pnr'] ?? null;
            
            if (isset($data['passengers'])) {
                $data['passengers'] = json_encode($data['passengers']);
            }

            $ticket = TicketInfo::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'operator_id' => $data['operator_id'],
                    'source' => $data['origin'],
                    'destination' => $data['destination'],
                    'parent_route' => $data['parent_route'],
                    'date_of_journey' => $data['date_of_journey'],
                ],
                $data
            );

            return response()->json([
                'status' => 200,
                'message' => 'ticket stored successfully',
                'data' => $ticket
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ]);
        }
    }

    public function getUserTickets(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'date_of_journey' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ]);
            }

            $query = TicketInfo::where('user_id', $request->user_id);

            if ($request->has('date_of_journey')) {
                $query->where('date_of_journey', $request->date_of_journey);
            }

            $tickets = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 200,
                'message' => 'tickets fetched successfully',
                'data' => $tickets
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
