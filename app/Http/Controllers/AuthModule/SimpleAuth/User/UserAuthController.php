<?php

namespace App\Http\Controllers\AuthModule\SimpleAuth\User;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class UserAuthController extends Controller
{
    public function userRegister(Request $request)
    {
        try {
            
            $validator = Validator::make($request->form, [
                'name' => 'required|string',
                'gender' => 'required|string',
                'phone' => 'required|unique:users,phone',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validation failed',
                    'errors' => $validator->messages()
                ], 422);
            }

                $data = $request->form;

                $user = User::create([
                    'name'     => $data['name'],
                    'gender'   => $data['gender'],
                    'phone'    => $data['phone'],
                    'role'     => 'customer',
                    'password' => Hash::make($data['name']),
                    'pswView'  => $data['name'],
                ]);

            $loginData = $this->autoLogin($user);

            return response()->json([
                'status' => 200,
                'message' => 'User registered successfully',
                'data' => [
                    'userData' => $user,
                    'loginData' => $loginData
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function autoLogin($user) {
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['token_type' => 'Bearer', 'access_token' => $token];
    }


    public function userLogin(Request $request) {
        try {
            $phone = $request->phone;
            $user = User::where('phone', $phone)->first();
            
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['status' => 401, 'message' => 'Incorrect password']);
            }

            if ($user) {
                $user->tokens()->delete();
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'message' => 'Login successful',
                    'data' => [
                        'userData' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'gender' => $user->gender,
                        ],
                        'loginData' => [
                            'access_token' => $token,
                            'token_type' => 'Bearer',
                        ]
                    ]
                ]);
            }

            return response()->json([
                'status' => 404,
                'message' => 'User not found',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}
