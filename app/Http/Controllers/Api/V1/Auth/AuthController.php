<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Notifications\VerifyEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\V1\BaseController;

class AuthController extends BaseController
{
    public function register(Request $request)
    {

        $validateUser = Validator::make($request->all(), [
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email' , 'email:exists'],
            'phone' => ['required', 'numeric', 'digits:10', 'regex:/^9[0-9]{9}$/'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'gender' => ['required','in:male,female,other'],
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

        // $user->notify(new VerifyEmail($user));

        Patient::create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'You have registered successfully'
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
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Email or password is incorrect',
            ], 404);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'User logged out successfully',
        ], 200);
    }

    public function changePassword(Request $request)
    {
        // Validate the request data
        $validateAppointment = Validator::make($request->all(), [
            'current_password' => ['required', 'string', 'min:8'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    
        // Check if validation fails
        if ($validateAppointment->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validateAppointment->errors(),
            ], 422);
        }
    
        // Find the user
        $user = Auth::user();
        $id = $user->id;
        $user = User::find($id);
    
        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 401);  // Use 401 Unauthorized for incorrect current password
        }
    
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
    
        return response()->json([
            'message' => 'Password changed successfully',
        ], 200);  // 200 OK when the password is changed successfully
    }
    
}
