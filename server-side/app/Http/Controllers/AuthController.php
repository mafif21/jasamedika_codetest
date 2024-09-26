<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','refresh','logout']]);
    }

    public function register(RegisterRequest $request){
        try {
            $user = User::create($request->validated());
            $token = Auth::guard('api')->login($user);

            $data = [
                'user' => $user,
                'token' => $token,
                'type' => 'Bearer'
            ];

            return ApiResponseClass::sendResponse($data, 'success register', 201);
        }catch (\Exception $e){
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $credentials = $request->only('email', 'password');

            $token = Auth::guard('api')->attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            $user = Auth::guard('api')->user();
            $data = [
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ];

            return ApiResponseClass::sendResponse($data, 'success login', 201);
        }catch (\Exception $e){
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();
            return ApiResponseClass::sendResponse(null, 'success logout', 200);
        }catch (\Exception $e){
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }

    }

    public function refresh()
    {
        try{
            $data = [
                'user' => Auth::guard('api')->user(),
                'authorisation' => [
                    'token' => Auth::guard('api')->refresh(),
                    'type' => 'bearer',
                ]
            ];
            return ApiResponseClass::sendResponse($data, 'success refresh', 200);

        }catch (\Exception $e){
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }
}
