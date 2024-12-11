<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\V1\BaseController;


class PatientController extends BaseController
{
    public function index()
    {
        // if (Auth::user()->roles == 'patient') {
            $userId = Auth::user()->id;
            $user = User::find($userId);
            $patient = Patient::where('user_id', $userId)->first();
            return response()->json([
                'patient' => [
                    'id' => $patient->id,
                    'user_id' => $patient->user_id,
                    'f_name' => $patient->user->f_name,
                    'l_name' => $patient->user->l_name,
                    'email' => $patient->user->email,
                    'phone' => $patient->user->phone,
                    'address' => $patient->user->address,
                    'age' => $patient->user->age,
                    'gender' => $patient->user->gender,
                    'blood_group' => $patient->user->blood_group
                ]
            ]);
        // }
        // return $this->errorResponse('You are not a patient');
    }


    public function update(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'digits:10', 'regex:/^9[0-9]{9}$/'],
            'address' => ['required', 'string', 'max:255'],
            'age' => ['required', 'numeric'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'blood_group' => ['required', 'string'],
            'password' => ['nullable', 'required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validate->errors(),
            ], 422);
        }


        $user = Auth::user()->id;
        $user = User::find($user);
        // Update patient information
        $user->update([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => $request->password, // Use hashed password
            'age' => $request->age,
            'gender' => $request->gender,
            'blood_group' => $request->blood_group,
        ]);

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Your profile updated successfully'
        ], 200);
    }
}
