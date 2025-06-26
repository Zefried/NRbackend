<?php

namespace App\Http\Controllers\BookingModule\BookingLayout\GenerateLayout;

use App\Http\Controllers\Controller;
use App\Models\BookingModule\Bookings\LayoutDetail\BookingLayoutDetail;
use App\Models\BookingModule\Bookings\LayoutMaster\BookingLayoutMaster;
use App\Models\ConfigModule\Layouts\Standard\StandardLayDetail\StandardLayDetail;
use App\Models\ConfigModule\Layouts\Standard\StandardLayMaster\StandardLayMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateLayoutController extends Controller
{
    public function resource(Request $request)
    {
        $type = $request->type;
        
        if ($type === 'generate') {
            return $this->generateBookingLayout($request);
        }

        return response()->json(['status' => false, 'message' => 'Invalid type'], 400);
    }

    public function generateBookingLayout(Request $request)
    {
        DB::beginTransaction();

        try {
            $operator_id = $request->operator_id;
            $date = $request->date;
            $parent_route = $request->parent_route;

            $layout = StandardLayMaster::where('operator_id', $operator_id)->first();

            if (!$layout) {
                return response()->json([
                    'status' => 404,
                    'message' => 'layout not found for this operator'
                ], 404);
            }

            $details = StandardLayDetail::where('layout_id', $layout->id)->get();

            $master = BookingLayoutMaster::where('operator_id', $operator_id)
                ->where('date', $date)
                ->where('parent_route', $parent_route)
                ->first();

            if (!$master) {
                $master = BookingLayoutMaster::create([
                    'operator_id' => $operator_id,
                    'date' => $date,
                    'parent_route' => $parent_route,
                    'master_key' => $this->generateMasterKey()
                ]);
            }

            $this->handleBookingLayoutDetail($master, $layout, $details);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'booking layout created successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage(),
            ], 500);
        }
    }

    private function handleBookingLayoutDetail($master, $layout, $details)
    {
        if ($layout->seater) {
            $total = $details->where('type', 'seater')->sum(fn($d) => $d->row * $d->col);

            if (!BookingLayoutDetail::where('master_key_id', $master->master_key)->where('seat_type', 'seater')->exists()) {
                BookingLayoutDetail::create([
                    'master_key_id' => $master->master_key,
                    'seat_type' => 'seater',
                    'total_seats' => $total,
                    'available_seats' => $total,
                    'double_seats' => json_encode($this->generateDoubleSeatNo($total)),
                    'female_booked' => null,
                    'available_for_female' => null,
                    'booked' => null,
                ]);
            }
        }

        if ($layout->sleeper) {
            $total = $details->where('type', 'sleeper')->sum(fn($d) => $d->row * $d->col);

            if (!BookingLayoutDetail::where('master_key_id', $master->master_key)->where('seat_type', 'sleeper')->exists()) {
                BookingLayoutDetail::create([
                    'master_key_id' => $master->master_key,
                    'seat_type' => 'sleeper',
                    'total_seats' => $total,
                    'available_seats' => $total,
                    'double_seats' => json_encode($this->generateDoubleSeatNo($total)),
                    'female_booked' => null,
                    'available_for_female' => null,
                    'booked' => null,
                ]);
            }
        }

        if ($layout->double_sleeper) {
            $upper = $details->where('type', 'upper')->sum(fn($d) => $d->row * $d->col);
            $lower = $details->where('type', 'lower')->sum(fn($d) => $d->row * $d->col);

            if ($upper > 0 && !BookingLayoutDetail::where('master_key_id', $master->master_key)->where('seat_type', 'upper')->exists()) {
                BookingLayoutDetail::create([
                    'master_key_id' => $master->master_key,
                    'seat_type' => 'upper',
                    'total_seats' => $upper,
                    'available_seats' => $upper,
                    'double_seats' => json_encode($this->generateDoubleSeatNo($upper)),
                    'female_booked' => null,
                    'available_for_female' => null,
                    'booked' => null,
                ]);
            }

            if ($lower > 0 && !BookingLayoutDetail::where('master_key_id', $master->master_key)->where('seat_type', 'lower')->exists()) {
                BookingLayoutDetail::create([
                    'master_key_id' => $master->master_key,
                    'seat_type' => 'lower',
                    'total_seats' => $lower,
                    'available_seats' => $lower,
                    'double_seats' => json_encode($this->generateDoubleSeatNo($lower)),
                    'female_booked' => null,
                    'available_for_female' => null,
                    'booked' => null,
                ]);
            }
        }
    }

    private function generateDoubleSeatNo($totalSeats)
    {
        try {
            $doubleSide = [];

            for ($i = 2; $i < $totalSeats; $i += 3) {
                if ($i + 1 <= $totalSeats) {
                    $doubleSide[] = [$i, $i + 1];
                }
            }

            return $doubleSide;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function generateMasterKey(): string
    {
        do {
            $key = 'M' . now()->format('dHi') . Str::random(3);
        } while (BookingLayoutMaster::where('master_key', $key)->exists());

        return $key;
    }




}
