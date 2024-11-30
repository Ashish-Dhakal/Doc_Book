<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Notifications\VerifyEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\UserRequest\UserUpdateRequest;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['patients'] = Patient::all();
        $data['doctors'] = Doctor::all();

        return $this->successResponse($data ,'patient and doctor list');

        // return response()->json([
        //     'success' => true,
        //     'patietns' => $data['patients'],
        //     'doctors' => $data['doctors'],
        //     'message' => 'patient and doctor list'
        // ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            'roles' => ['required'],
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
            'phone' => $request->phone,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_group' => $request->blood_group,
            'address' => $request->address,
            'email' => $request->email,
            'roles' => $request->roles,
            'password' => bcrypt($request->password),
        ]);

        // $user->notify(new VerifyEmail($user));

        Patient::create([
            'user_id' => $user->id,
        ]);

        return $this->successResponse($user ,'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
        
            return response()->json([
                'status' => true,
                'message' => 'User details',
                'user_detail' => $user
            ], 200);

            return $this->successResponse($user ,'User details');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'No user found of that id'
            ], 404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id):JsonResponse
    {
        $user = User::find($id);

           $validateUser = Validator::make($request->all(), [
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['required', 'numeric'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'gender' => ['required'],
            'age' => ['required', 'numeric'],
            'roles' => ['required'],
            'blood_group' => ['required'],
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validateUser->errors()->all(),
            ]);
        }

        $user->update([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'age' => $request->age,
            'blood_group' => $request->blood_group,
            'address' => $request->address,
            'email' => $request->email,
            'roles' => $request->roles,
            'password' => bcrypt($request->password),
        ]);

        if ($user->roles == 'patient') {
            $patient = Patient::where('user_id', $user->id)->first();

            if ($patient) {
                $patient->update([
                    'user_id' => $user->id,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Patient not found'
                ], 404);
            }
        }

        if ($user->roles == 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();

            if ($doctor) {
                $doctor->update([
                    'user_id' => $user->id,
                    'speciality_id' => $request->speciality_id,
                    'hourly_rate' => $request->hourly_rate,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Doctor not found'
                ], 404);
                // return redirect()->back()->with('error', 'Doctor not found');
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
        ], 200);

        // return redirect()->route('users.index')->with('success', 'User updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }
}
