<?php

namespace App\Http\Controllers\BookingModule\Tickets;

use App\Http\Controllers\Controller;
use App\Models\BookingModule\PNR\Detail\PnrDetail;
use App\Models\BookingModule\Ticket\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function resource(Request $request)
    {
        $type = $request->type;

        if ($type === 'store') {
            return $this->store($request);
        }

        return response()->json(['status' => false, 'message' => 'Invalid type'], 400);
    }

    private function store($request)
    {
        $pnr = $request->pnr;

        // Fetch passenger details using the given PNR
        $passengers = PnrDetail::where('pnr', $pnr)->get([
            'seat_no',
            'name',
            'gender'
        ]);

        if ($passengers->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No passenger details found for this PNR'
            ], 404);
        }

        // Prepare ticket data
        $ticket = Ticket::firstOrCreate(
            ['pnr' => $pnr],
            [
                'user_id' => $request->user_id,
                'operator_id' => $request->operator_id,
                'date' => $request->date,
                'parent_route' => $request->parent_route,
                'total_seats' => $request->total_seats,
                'total_fair' => $request->total_fair,
                'passengers' => $passengers->toJson(),
            ]
        );


        return response()->json([
            'status' => 200,
            'message' => 'Ticket details created successfully',
            'data' => $ticket
        ]);
    }

  
}
