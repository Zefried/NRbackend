<?php

namespace App\Http\Controllers\Orders\OrderRealTime;

use App\Http\Controllers\Controller;
use App\Models\BusConfig\Bookings\Bookings;
use App\Models\BusConfig\Orders\Orders;
use App\Models\BusConfig\RealTimeSeatHoldingStatus\RealTimeSeatHolding;
use App\Models\BusConfig\SeatConfig\SeatConfig;
use App\Models\BusConfig\SeatHoldingConfig\SeatHoldingConfig;
use App\Models\BusConfig\SleeperConfig\SleeperConfig;
use App\Models\BusConfig\VipConfig\VipConfig;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderRealTimeController extends Controller
{


    public function createOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bus_id' => 'required|nullable|string',
                'bus_name_plate' => 'nullable|string',
                'customer_name' => 'nullable|string',
                'gender' => 'nullable|string',
                'age' => 'nullable|integer',
                'boarding' => 'nullable|string',
                'user_phone' => 'required',
                'dropping' => 'nullable|string',
                'seat_no_hold' => 'nullable',
                'seat_type' => 'nullable|string',
                'amount' => 'nullable|numeric',
                'order_status' => 'nullable|string',
                'user_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['validation_error' => $validator->messages()]);
            }


            // check seat hold if true then send already booked 

            $data = Orders::create([
                'bus_id' => $request->bus_id,
                'user_id' => $request->user_id,
                'bus_name_plate' => $request->bus_name_plate,
                'customer_name' => $request->customer_name,
                'user_phone' => $request->user_phone,
                'gender' => $request->gender,
                'age' => $request->age,
                'boarding' => $request->boarding,
                'dropping' => $request->dropping,
                'amount' => $request->amount,
                'order_status' => $request->order_status,
            ]);

      

            if ($data) {
    
              $seatHoldingData = $this->handleSeatHoldingConfig($request->seat_no_hold, $data);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'order saved',
                    'data' => $data,
                    'seatHoldingData' => $seatHoldingData
                ]);
            }

        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function handleSeatHoldingConfig($seatHoldData, $order)
    {
        $response = [];

        foreach ($seatHoldData as $type => $seats) {
            
            $confirmingSeatHolding = SeatHoldingConfig::create([
                'order_id' => $order->id,
                'bus_id' => $order->bus_id,
                'user_id' => $order->user_id,
                'seat_no_hold' => json_encode($seats),  // poora seat array yahan JSON mein
                'seat_type' => $type,
                'holding_disable' => false
            ]);
            $response[] = $confirmingSeatHolding;
        }

        return $response;
    }
    

    public function generateLink(Request $request){
        return response()->json('test');
    }
    
    // When someone clicks on "Pay Now", the link generation function should run  
    // and return the short link to the user. Then Razorpay should callback.  
    // The paymentStatus function should run next.  
    // If payment fails, ask the user to retry payment (or handle accordingly).  
    // If payment succeeds, update the status as the first step.  
    // Then create the booking by fetching order data.  



    public function paymentStatus(Request $request)
    {
        try {

            // order id we will find from razorpay callback data
            // using the order id we will find the orderData 

            // Fetching orders made within the last 48 hours for a specific user_id

            $order = Orders::where('id', 1)->with('orderSeatConfig')->get();

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // payment status bhi call back url se milega but for now 
            $status = $request->payment_status;

            if ($status === 'paid') {
            

                $bookingProcess = $order->update([
                    'payment_status' => $status,
                ]);


                if ($bookingProcess) {
                          
                    $bookings = $this->createBooking($order);

                    if ($bookings) {
                     
                        $seatConfigStatus = $this->seatConfigRun($order->id);

                        return response()->json([
                            'status' => 200,
                            'message' => 'Booking successful',
                            'data' => $seatConfigStatus,
                        ]);
                        
                    } else {

                        return response()->json([
                            'status' => 500,
                            'message' => 'Payment received but booking failed. Contact support with your transaction ID.',
                        ]);
                    }
                } else {

                    return response()->json([
                        'status' => 500,
                        'message' => 'Failed to update payment status.',
                    ]);
                }

            } else {

                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid payment status.',
                ]);

            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function createBooking(Orders $order){
           
            $bookings = Bookings::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'gender' => $order->gender,
                'bus_id' => $order->bus_id,
                'user_phone' => $order->user_phone,
                'transaction_id' => 'txn_' . uniqid(),
                'seat_type' => $order->seat_type,
                'seat_no' => $order->seat_no_hold,
                'boarding' => $order->boarding,
                'dropping' => $order->dropping,
                'amount' => $order->amount,
                'payment_status' => $order->payment_status,
            ]);

        // also this function to background que
        return $bookings;


        // now create an booking
        // if booking true then fix seat config based on sleeper or SS
        // last return seat configs
        // run the frontend 
        // ek hi baar mai vip, SS, sleeper sara bana le seat config 

    }


    public function seatConfigRun($orderId)
    {
        $bookingData = Bookings::where('order_id', $orderId)->first();

        if (!$bookingData) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        if ($bookingData->seat_type === 'sleeper') {    
            return 3;

            $storeData = $this->storeSleeperConfig($bookingData);

        } elseif ($bookingData->seat_type === 'vip') {

            $storeData = $this->storeVipConfig($bookingData);

        } else {
            $storeData = $this->storeSeatingSeater($bookingData);

            if ($storeData) {
                return response()->json($storeData); 
            } else {
                return response()->json(['error' => 'SeatConfig not found'], 404);
            }
        }

    }

    public function storeSeatingSeater($bookingData)
    {
        // we are using seatConfig model to store seating seaters however for sleeper and vip 
        // we have different models 

        $busId = $bookingData->bus_id;
        $seatConfig = SeatConfig::where('bus_id', $busId)->first();

        if ($seatConfig) {

            $booked = json_decode($seatConfig->booked, true) ?? [];
            $seatConfig->booked = json_encode(array_merge($booked, [$bookingData->seat_no]));

            if ($bookingData->gender === 'female') {
                $femaleBooked = json_decode($seatConfig->booked_by_female, true) ?? [];
                $seatConfig->booked_by_female = json_encode(array_merge($femaleBooked, [$bookingData->seat_no]));
            }

            if ($bookingData->gender === 'other') {
                $otherBooked = json_decode($seatConfig->booked_by_other, true) ?? [];
                $seatConfig->booked_by_other = json_encode(array_merge($otherBooked, [$bookingData->seat_no]));
            }

            $updatedBooked = json_decode($seatConfig->booked, true) ?? [];
            $seatConfig->currently_avl = $seatConfig->total_seats - count($updatedBooked);
            $seatConfig->save();

            return $seatConfig;
        }

        return null;
    }


    public function storeSleeperConfig($bookingData)
    {
        $busId = $bookingData->bus_id;
        $seatConfig = SleeperConfig::where('bus_id', $busId)->first();

        if ($seatConfig) {

            $booked = json_decode($seatConfig->booked, true) ?? [];
            $seatConfig->booked = json_encode(array_merge($booked, [$bookingData->seat_no]));

            if ($bookingData->gender === 'female') {
                $femaleBooked = json_decode($seatConfig->booked_by_female, true) ?? [];
                $seatConfig->booked_by_female = json_encode(array_merge($femaleBooked, [$bookingData->seat_no]));
            }

            if ($bookingData->gender === 'other') {
                $otherBooked = json_decode($seatConfig->booked_by_other, true) ?? [];
                $seatConfig->booked_by_other = json_encode(array_merge($otherBooked, [$bookingData->seat_no]));
            }

            $updatedBooked = json_decode($seatConfig->booked, true) ?? [];
            $seatConfig->currently_avl = $seatConfig->total_seats - count($updatedBooked);
            $seatConfig->save();

            return $seatConfig;
        }

        return null;
    }
  

    public function storeVipConfig($bookingData)
    {
        $busId = $bookingData->bus_id;
        $seatConfig = VipConfig::where('bus_id', $busId)->first();

        if ($seatConfig) {

            $booked = json_decode($seatConfig->booked, true) ?? [];
            $seatConfig->booked = json_encode(array_merge($booked, [$bookingData->seat_no]));

            if ($bookingData->gender === 'female') {
                $femaleBooked = json_decode($seatConfig->booked_by_female, true) ?? [];
                $seatConfig->booked_by_female = json_encode(array_merge($femaleBooked, [$bookingData->seat_no]));
            }

            if ($bookingData->gender === 'other') {
                $otherBooked = json_decode($seatConfig->booked_by_other, true) ?? [];
                $seatConfig->booked_by_other = json_encode(array_merge($otherBooked, [$bookingData->seat_no]));
            }

            $updatedBooked = json_decode($seatConfig->booked, true) ?? [];
            $seatConfig->currently_avl = $seatConfig->total_seats - count($updatedBooked);
            $seatConfig->save();

            return $seatConfig;
        }

        return null;
    }


    /// initial step of seat booking

    public function realTimeSeatHoldingStatus(Request $request)
    {
        try {
            $seatNo = $request->seat_no;

            $expiredTime = now()->subMinutes(5);
            RealTimeSeatHolding::where('created_at', '<', $expiredTime)->delete();

            $exists = RealTimeSeatHolding::where('bus_id', $request->bus_id)
                ->where('seat_no', $seatNo)
                ->where('seat_type', $request->seat_type)
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




}
