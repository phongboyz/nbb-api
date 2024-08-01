<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Auth\LoginResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'password'  => 'required|string'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $credentials    =   $request->only('name', 'password');

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }else{
            $user   = User::where('name', $request->name)->firstOrFail();
            $token  = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'message'       => 'success',
                'user'          => $user,
                'access_token'  => $token,
                'token_type'    => 'Bearer'
            ], 200);

            // return LoginResource::make($user);
        }
    }

    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout successfull'
        ]);
    }
}
