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
     * Fetch all users
     */
    public function index()
    {
        // $data['patients'] = Patient::with('user:id,f_name,l_name,email') // Select only specific user fields
        $data['patients'] = Patient::with('user') // Select only specific user fields
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'user' => [
                        'id' => $patient->user->id,
                        'f_name' => $patient->user->f_name,
                        'l_name' => $patient->user->l_name,
                        'email' => $patient->user->email,
                        'blood_group' => $patient->user->blood_group,
                        'gender' => $patient->user->gender,
                        'age' => $patient->user->age,
                        'phone' => $patient->user->phone,
                        'address' => $patient->user->address,
                        'roles' => $patient->user->roles
                    ],
                ];
            });


        $data['doctors'] = Doctor::with('user')->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'user' => [
                        'id' => $doctor->user->id,
                        'f_name' => $doctor->user->f_name,
                        'l_name' => $doctor->user->l_name,
                        'hourly_rate' => $doctor->hourly_rate,
                        'email' => $doctor->user->email,
                        'blood_group' => $doctor->user->blood_group,
                        'gender' => $doctor->user->gender,
                        'age' => $doctor->user->age,
                        'phone' => $doctor->user->phone,
                        'address' => $doctor->user->address,
                        'roles' => $doctor->user->roles
                    ],
                ];
            });

        return $this->successResponse($data, 'patient and doctor list');
    }

    /**
     * Create a new user
     */
    public function store(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'numeric', 'digits:10', 'regex:/^9[0-9]{9}$/'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'gender' => ['required','in:male,female'],
            'age' => ['required', 'numeric'],
            'roles' => ['required','in:doctor,patient'],
            'blood_group' => ['required'],
        ]);

        if ($validateUser->fails()) {
            return $this->errorResponse($validateUser->errors());
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

        $userRole = $user->roles;

        switch ($userRole) {
            case 'patient':
                Patient::create([
                    'user_id' => $user->id,
                ]);
                return $this->successResponse($user, 'Patient created successfully');
                break;
            case 'doctor':
                Doctor::create([
                    'user_id' => $user->id,
                    'speciality_id' => $request->speciality_id,
                    'hourly_rate' => $request->hourly_rate
                ]);
                return $this->successResponse($user, 'Patient created successfully');
                break;
            default:
                break;
        }
    }

    /**
     * Display the user
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user_data = [
                'id' => $user->id,
                'f_name' => $user->f_name,
                'l_name' => $user->l_name,
                'email' => $user->email,
                'roles' => $user->roles,
                'gender' => $user->gender,
                'age' => $user->age,
                'blood_group' => $user->blood_group,
                'phone' => $user->phone,
                'address' => $user->address,
            ];
            if ($user->roles === 'doctor') {
                $doctor = Doctor::where('user_id', $user->id)->with('speciality')->first();
                if ($doctor) {
                    $user_data['speciality_name'] = $doctor->speciality->name ?? 'Not Available';
                    $user_data['hourly_rate'] = $doctor->hourly_rate ?? 'Not Available';
                }
            }
        
            return $this->successResponse($user_data, 'User details');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'No user found of that id'
            ], 404);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        $validateUser = Validator::make($request->all(), [
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['required', 'numeric', 'digits:10', 'regex:/^9[0-9]{9}$/'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'gender' => ['required','in:male,female,other'],
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
     * Delete the user
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
