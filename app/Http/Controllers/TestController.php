<?php

namespace App\Http\Controllers;

use App\Models\BusConfig\SeatConfig\SeatConfig;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function findGender() {
        $userId = 2;
        $busId = 2;

        $gender = SeatConfig::where('id', $busId)
            ->with(['orders' => function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereDate('created_at', today());
            }])
            ->first()
            ?->orders
            ->first()
            ?->gender;

        return $gender;
    }

}
