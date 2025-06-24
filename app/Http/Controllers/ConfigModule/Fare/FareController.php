<?php

namespace App\Http\Controllers\ConfigModule\Fare;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        return response()->json('hit');
    }
}
