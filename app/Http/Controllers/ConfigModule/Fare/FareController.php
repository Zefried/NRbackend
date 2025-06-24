<?php

namespace App\Http\Controllers\ConfigModule\Fare;

use App\Http\Controllers\Controller;
use App\Models\ConfigModule\Fare\LowerBerthFare;
use App\Models\ConfigModule\Fare\SeaterFare;
use App\Models\ConfigModule\Fare\SleeperFare;
use App\Models\ConfigModule\Fare\UpperBerthFare;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FareController extends Controller
{
     public function resource(Request $request)
    {
        $type = $request->type;

        if ($type === 'store') {
            return $this->store($request);
        }

        // if($type == 'retrieve'){
        //     return $this->fetch($request);
        // }

        return response()->json(['status' => false, 'message' => 'Invalid type'], 400);
    }

    private function store($request)
    {
        try {
            $data = $request->data;

            foreach ($data as $item) {
                $type = $item['seat_type'] ?? null;

                $model = match ($type) {
                    'seater' => SeaterFare::class,
                    'sleeper' => SleeperFare::class,
                    'upper' => UpperBerthFare::class,
                    'lower' => LowerBerthFare::class,
                    default => null,
                };

                if (!$model) {
                    return response()->json([
                        'status' => 400,
                        'message' => "Invalid seat type in item: " . json_encode($item),
                    ], 400);
                }

                $validator = Validator::make($item, [
                    'serving_route_id' => 'required|exists:serving_routes,id',
                    'fare' => 'required|numeric|min:0',
                    'discount_flat' => 'nullable|numeric|min:0',
                    'discount_percent' => 'nullable|numeric|min:0|max:100',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'validation error',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $finalFare = $item['fare'];
                if (!empty($item['discount_flat'])) {
                    $finalFare -= $item['discount_flat'];
                } elseif (!empty($item['discount_percent'])) {
                    $finalFare -= ($item['fare'] * $item['discount_percent']) / 100;
                }

                if ($finalFare < 0) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'final fare cannot be negative',
                    ], 422);
                }

                $model::updateOrCreate(
                    [
                        'serving_route_id' => $item['serving_route_id'],
                        'type' => $type,
                    ],
                    [
                        'fare' => $item['fare'],
                        'discount_flat' => $item['discount_flat'] ?? null,
                        'discount_percent' => $item['discount_percent'] ?? null,
                        'final_fare' => round($finalFare, 2),
                    ]
                );
            }

            return response()->json([
                'status' => 200,
                'message' => 'fare data stored/updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ], 500);
        }

    }
}
