<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Notifications\VerifyEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validateUser = Validator::make($request->all(), [
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'numeric'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'gender' => ['required'],
            'age' => ['required', 'numeric'],
            'blood_group' => ['required'],
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validateUser->errors()->all(),
            ]);
        }

        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_group' => $request->blood_group,
            'address' => $request->address,
            'phone' => $request->phone,
            'password' => $request->password,
        ]);

        $user->notify(new VerifyEmail($user));

        Patient::create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'user' => $user
        ], 200);
    }
    public function login(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication failed',
                'errors' => $validateUser->errors()->all(),
            ], 404);
        }
        if (Auth::attempt($request->only('email', 'password'))) {
            $authUser = Auth::user();
            return response()->json([
                'status' => true,
                'message' => 'User Loggedin successfully',
                'token' => $authUser->createToken('authToken')->plainTextToken,
                'token_type' => 'bearer'
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Email or password is incorrect',
            ], 404);
        }
    }

    public function logout(Request $request) {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,  
            'user' => $user,
            'message' => 'User logged out successfully',
        ], 200);
    }
}
