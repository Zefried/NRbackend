<?php

namespace App\Http\Controllers\Auth\AdminAuth;


use App\Http\Controllers\Controller;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    
    public function adminLogin(Request $request)
    {

        try{

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if($validator->fails()){
                return response()->json(['validation_error' => $validator->messages()]);
            }
    
            $userData = User::where('email', $request->email)->first();

            if ($userData && Hash::check($request->password, $userData->password)) {

                // Delete old tokens
                if ($userData->tokens()->exists()) {
                    $userData->tokens()->delete();
                }

                // Create new token
                $token = $userData->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'token' => $token,
                    'user' => $userData,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials.',
            ]);

        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
       
    }

}
