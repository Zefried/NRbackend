<?php

namespace App\Http\Controllers\Orders\BookingRealTime;

use App\Http\Controllers\Controller;
use App\Models\BusConfig\AddBus\AddBus;
use App\Models\BusConfig\Bookings\Bookings;
use App\Models\BusConfig\BusRouteInfo\BusRouteInfoModel;
use App\Models\BusConfig\Orders\Orders;
use App\Models\BusConfig\PNR\PNRModel;
use App\Models\BusConfig\RealTimeSeatHoldingStatus\RealTimeSeatHolding;
use App\Models\BusConfig\TicketModel\TicketModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{   
    public function realTimeSeatHoldingStatus(Request $request)
    {
        try {
            $seatNo = $request->seat_no;

            $expiredTime = now()->subMinutes(5);
            RealTimeSeatHolding::where('created_at', '<', $expiredTime)->delete();

            $exists = RealTimeSeatHolding::where('bus_id', $request->bus_id)
                ->where('seat_no', $seatNo)
                ->where('seat_type', $request->seat_type)
                ->where('origin', $request->origin)
                ->where('destination', $request->destination)
                ->exists();

            if ($exists) {
                
                $holdSeats = RealTimeSeatHolding::get(['seat_no']);
                
                return response()->json([
                    'message' => "Seat $seatNo already on hold",
                    'status' => true,
                    'data' => $holdSeats,
                ]);
            }


           $data = RealTimeSeatHolding::create([
                'bus_id' => $request->bus_id,
                'user_id' => $request->user_id,
                'seat_type' => $request->seat_type,
                'seat_no' => $request->seat_no,
                'origin' => $request->origin,
                'destination' => $request->destination,
            ]); 

            return response()->json([
                'message' => "Seat held successfully",
                'status' => 200,
                'seat_no' => $data->seat_no,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function realTimeSeatReleaseStatus(Request $request)
    {
        try {
            
            $deleted = RealTimeSeatHolding::where('bus_id', $request->bus_id)
                ->where('seat_type', $request->seat_type)
                ->where('user_id', $request->user_id)
                ->where('seat_no', $request->seat_no)
                ->delete();

            return response()->json([
                'message' => $deleted ? 'Seat released, please select new seat' : 'No matching seat found',
                'status' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function continueBooking(Request $request)
    {
        DB::beginTransaction();

        try {
            $pnr = $this->generatePNRCode();
            $totalSeats = $this->findTotalSeats($request->user_id, $request->bus_id);

            if ($totalSeats == 0) {
                return response()->json([
                    'seatHold' => false,
                    'message'  => 'Seat holding session has expired. Please book again.',
                    'redirect' => true,
                ]);
            }

            $booking = Bookings::create([
                'user_id'        => $request->user_id,
                'bus_id'         => $request->bus_id,
                'route_info_id'  => $request->route_info_id,
                'pnr_code'       => $pnr,
                'origin'         => $request->origin,
                'destination'    => $request->destination,
                'date_of_journey'    => $request->date_of_journey,
                'booking_status' => 'pending',
                'payment_status' => 'pending',
                'total_fare'     => $request->finalAmount,
                'total_seats'    => $totalSeats,
            ]);

            if (!$booking) throw new \Exception("Booking creation failed");

            $data = RealTimeSeatHolding::where('user_id', $booking->user_id)
                ->where('bus_id', $booking->bus_id)
                ->where('origin', $booking->origin)
                ->where('destination', $booking->destination)
                ->get();

            foreach ($data as $item) {
                $item->booking_id = $booking->id;
                $item->save();
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'data'   => $booking,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 500,
                'message' => 'Booking failed. Try again.',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function generatePNRCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (PNRModel::where('pnr_code', $code)->exists());

        return $code;
    }

    public function findTotalSeats($user_id, $bus_id){
        $expiredTime = now()->subMinutes(5);
        RealTimeSeatHolding::where('created_at', '<', $expiredTime)->delete();

        return RealTimeSeatHolding::where('user_id', $user_id)
            ->where('bus_id', $bus_id)
            ->count();
    }


     // passanger work starts here

    public function fetchPsgFields(Request $request)
    {
        try {
            $psgFields = RealTimeSeatHolding::where('bus_id', $request->bus_id)
                ->where('user_id', $request->user_id)
                ->where('origin', $request->origin)
                ->where('destination', $request->destination)
                ->get(['seat_type', 'seat_no']);

            return response()->json([
                'status' => 200,
                'data' => $psgFields,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong while fetching passenger fields.',
                'error' => $e->getMessage(),
            ]);
        }
    }

   
    public function addPsgData(Request $request)
    {
        // Step 1: Clear expired seat holds older than 5 minutes
        $expiredTime = now()->subMinutes(5);
        RealTimeSeatHolding::where('created_at', '<', $expiredTime)->delete();

        // Step 2: Check valid seat hold with booking_id
        $exist = RealTimeSeatHolding::where('bus_id', $request->bus_id)
            ->where('user_id', $request->user_id)
            ->where('origin', $request->origin)
            ->where('destination', $request->destination)
            ->whereNotNull('booking_id')
            ->first(['booking_id']);

        if (!$exist) {
            return response()->json([
                'status' => 200,
                'expiredSeats' => true,
                'message' => 'Seats expired. Please book again.'
            ]);
        }

        $booking = Bookings::find($exist->booking_id);
        $pnr = $booking->pnr_code ?? null;

        DB::beginTransaction();
        try {
            foreach ($request->passengers as $item) {
                PNRModel::create([
                    'booking_id' => $booking->id,
                    'pnr_code'   => $pnr,
                    'seat_type'  => $item['seat_type'],
                    'seat_no'    => $item['seat_no'],
                    'name'       => $item['name'],
                    'gender'     => $item['gender']
                ]);
            }
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Passenger data added.',
                'data' => $request->passengers
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Passenger data save failed.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function fetchPayOnBoardData(Request $request)
    {
        try {
            $bookingData = Bookings::select('pnr_code', 'total_fare', 'origin', 'destination', 'date_of_journey')
                ->where('id', $request->booking_id)
                ->where('date_of_journey', $request->date_of_journey)
                ->first();

            $routeInfoData = BusRouteInfoModel::select('boarding_points')
                ->where('id', $request->routeInfoId)
                ->where('bus_id', $request->bus_id)
                ->where('origin', $request->origin)
                ->where('destination', $request->destination)
                ->first();

            $busData = AddBus::select('operator_name', 'bus_config', 'bus_plate_number', 'driver_name', 'driver_phone', 'driverTwo_name', 'driverTwo_phone')
                ->where('id', $request->bus_id)
                ->first();

            $userPnr = $bookingData->pnr_code ?? null;

            $seatData = PNRModel::where('booking_id', $request->booking_id)
                ->where('pnr_code', $userPnr)
                ->get(['seat_type', 'seat_no', 'name', 'gender']);

            return response()->json([
                'status' => 200,
                'data' => [
                    'bookingData' => $bookingData,
                    'routeInfoData' => $routeInfoData,
                    'busData' => $busData,
                    'seatData' => $seatData
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ]);
        }
    }                           

    // payment function starts here 

    public function generateUniqueTicketNo() {
        do { $ticketNo = 'TICK-' . strtoupper(uniqid()); } while (TicketModel::where('unique_ticket_no', $ticketNo)->exists());
        return $ticketNo;
    }

    public function payOnBoard(Request $request){
        $expiredTime = now()->subMinutes(5);
        RealTimeSeatHolding::where('created_at', '<', $expiredTime)->delete();

        try {

            $exist = RealTimeSeatHolding::where('user_id',$request->user_id)
            ->where('bus_id', $request->bus_id)
            ->where('origin', $request->origin)
            ->where('destination', $request->destination)->exists();

            if(!$exist){
                return response()->json([
                    'expiredStatus' => true,
                    'message' => 'seat holding session expired',
                ]);
            }

            $ticket = TicketModel::create([
                'user_id'         => $request->user_id,
                'bus_id'          => $request->bus_id,
                'route_info_id'   => $request->routeInfoId,
                'operator_name'   => $request->operator,
                'driver_name'     => $request->driverName,
                'driver_number'   => $request->driverPhone,
                'driver_two_name' => $request->driverTwoName,
                'driver_two_number'=> $request->driverTwoPhone,
                'unique_ticket_no'=> $this->generateUniqueTicketNo(),
                'booking_id'      => $request->booking_id,
                'pnr_code'        => $request->pnr,
                'origin'          => $request->origin,
                'destination'     => $request->destination,
                'date_of_journey' => $request->date_of_journey,
                'payment_type'    => 'pay_on_board',
                'counter_no'      => '',
                'reporting_time'  => $request->reportingTime,
                'departure_time'  => $request->departureTime,
                'total_fare'      => $request->fare,
                'bus_type'        => $request->busType,
                'plate_no'        => $request->plateNo,
            ]);
            return response()->json(['status' => 200, 'message' => 'Ticket Generated Successfully', 'data' => $ticket]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Something went wrong while generating ticket.', 'error' => $e->getMessage()]);
        }
    }

    public function fetchMyTicket(Request $request)
    {
        try {
            $user_id = (int) $request->user_id;
            $tickets = TicketModel::where('user_id', $user_id)
                        ->orderBy('created_at', 'desc')
                        ->get();

            

            $allSeatData = collect();
            foreach($tickets as $ticket){
                $seats = PNRModel::where('pnr_code', $ticket->pnr_code)->get(['seat_no', 'seat_type', 'name', 'pnr_code']);
                $allSeatData = $allSeatData->merge($seats);
            }

            if ($tickets->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No tickets found for this user.'
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Tickets fetched successfully.',
                'data' => $tickets,
                'seatData' => $allSeatData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ]);
        }
    }





}
