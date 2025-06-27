<?php

namespace App\Http\Controllers\BookingModule\PNR;

use App\Http\Controllers\Controller;
use App\Models\BookingModule\Bookings\LayoutDetail\BookingLayoutDetail;
use App\Models\BookingModule\Bookings\LayoutMaster\BookingLayoutMaster;
use App\Models\BookingModule\PNR\Detail\PnrDetail;
use App\Models\BookingModule\PNR\Master\PnrMaster;
use App\Models\BookingModule\SeatHold\SeatHold;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PnrController extends Controller
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

   
    public function store($request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'operator_id' => 'required|integer',
            'parent_route' => 'required|string',
            'date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.name' => 'required|string',
            'details.*.seat_no' => 'required|string',
            'details.*.seat_type' => 'required|string',
            'details.*.gender' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'validation failed',
                'errors' => $validator->errors()
            ]);
        }

        try {
            DB::beginTransaction();
          
            $validHolds = [];
            foreach ($request->details as $detail) {
              
                $hold = SeatHold::where([
                    'user_id' => $request->user_id,
                    'operator_id' => $request->operator_id,
                    'parent_route' => $request->parent_route,
                    'date' => $request->date,
                    'seat_type' => $detail['seat_type'],
                    'seat_no' => $detail['seat_no'],
                ])->first();

                if ($hold) {
                    if ($hold->created_at->lt(now()->subMinutes(11))) {
                        $hold->delete();
                        return response()->json([
                            'status' => 410,
                            'message' => 'seat hold expired and removed',
                            'data' => $detail
                        ]);
                    } else {
                        $validHolds[] = $hold;
                    }
                }
            }

            $hold = SeatHold::where([
                    'user_id' => $request->user_id,
                    'operator_id' => $request->operator_id,
                    'parent_route' => $request->parent_route,
                    'date' => $request->date,
            ])->first(); // keep this hold it ensures consistency in code


            $master = PnrMaster::updateOrCreate(
                [
                    'user_id' => $hold->user_id,
                    'operator_id' => $hold->operator_id,
                    'parent_route' => $hold->parent_route,
                    'date' => $hold->date,
                ],
                [
                    'pnr' => $this->generateUniquePnrCode(),
                    'payment_status' => 'unpaid',
                    'pnr_status' => 'pending',
                ]
            );

            foreach ($request->details as $detail) {
                PnrDetail::updateOrCreate(
                    [
                        'pnr_master_id' => $master->id,
                        'seat_no' => $detail['seat_no'],
                    ],
                    [
                        'pnr' => $master->pnr,
                        'name' => $detail['name'],
                        'seat_type' => $detail['seat_type'],
                        'gender' => $detail['gender'],
                    ]
                );
            }

            if (!$this->handleBookingUpdate($request)) {
                DB::rollBack();
                return response()->json([
                    'status' => 500,
                    'message' => 'booking layout update failed'
                ], 500);
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'PNR Master with Details created successfully',
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ]);
        }
    }


    function generateUniquePnrCode()
    {
        do {
            $pnr = 'PNR' . strtoupper(Str::random(6));
        } while (PnrMaster::where('pnr', $pnr)->exists());

        return $pnr;
    }

    public function handleBookingUpdate($request)
    {
        $master = BookingLayoutMaster::where('operator_id', $request->operator_id)
            ->where('date', $request->date)
            ->where('parent_route', $request->parent_route)
            ->first();

        if (!$master) {
            return response()->json([
                'status' => 404,
                'message' => 'booking layout master not found'
            ]);
        }

        $grouped = collect($request->details)->groupBy('seat_type');

        foreach ($grouped as $type => $items) {

            $layoutDetail = BookingLayoutDetail::where('master_key_id', $master->master_key)
                ->where('seat_type', $type)
                ->first();

            if (!$layoutDetail) continue;

            $female = $layoutDetail->female_booked ? json_decode($layoutDetail->female_booked, true) : [];
            $available = $layoutDetail->available_for_female ? json_decode($layoutDetail->available_for_female, true) : [];
            $booked = $layoutDetail->booked ? json_decode($layoutDetail->booked, true) : [];
            $doubleSeat = $layoutDetail->double_seats ? json_decode($layoutDetail->double_seats, true) : [];

            foreach ($items as $entry) {
                $seat = (int) $entry['seat_no'];
                $gender = strtolower($entry['gender'] ?? '');

                $booked[] = $seat;

                if ($gender === 'female') {
                    $female[] = $seat;

                    $near = $this->findNearValue($seat, $doubleSeat);
                    if ($near) $available[] = $near;
                }
            }

            $booked = array_values(array_unique($booked));
            $female = array_values(array_unique($female));
            $available = array_values(array_unique($available));
            $availableSeats = $layoutDetail->total_seats - count($booked);

            $layoutDetail->update([
                'female_booked' => json_encode(array_values(array_unique($female))),
                'available_for_female' => json_encode(array_values(array_unique($available))),
                'booked' => json_encode(array_values(array_unique($booked))),
                'available_seats' => $availableSeats,
            ]);  
        }

        return true;

    }

    private function findNearValue($num, $arr)
    {
        foreach ($arr as $pair) {
            if (!is_array($pair)) continue;
            if (in_array($num, $pair)) {
                return $pair[0] === $num ? $pair[1] : $pair[0];
            }
        }
        return null;
    }


    // Turn the code into JS if needed to clarify logic
    // If [2,3] and 2 is booked by female, mark 3 as available for female
    // Start with abstract logic first—makes understanding the system easier




}
