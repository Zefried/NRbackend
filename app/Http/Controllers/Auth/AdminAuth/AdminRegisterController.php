<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers\Auth\AdminAuth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminRegisterController extends Controller
{

    public function adminRegister(request $request){

        try{

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
            ]);
    
            if($validator->fails()){
                return response()->json(['validation_error' => $validator->messages()]);
            }
    
            $data = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'pswView' => $request->password,
                'role' => 'admin',
            ]);
    
            return response()->json([
                'status' => 200,
                'message' => 'done',
                'data' => $data,
            ]);

        }catch(Exception $e){  
            return response()->json($e->getMessage());
        }

    }

}


