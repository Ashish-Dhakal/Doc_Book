<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use Illuminate\Http\Request;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest\UserCreateRequest;
use App\Http\Requests\UserRequest\UserUpdateRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['patients'] = Patient::paginate(5);
        $data['doctors'] = Doctor::paginate(5);
        return view('users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['specialities'] = Speciality::all();
        return view('users.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request)
    {
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


        if ($user->roles == 'patient') {
            Patient::create([
                'user_id' => $user->id,

            ]);
            return redirect()->route('users.index')->with('success', 'Patient created successfully');
        }
        if ($user->roles == 'doctor') {
            Doctor::create([
                'user_id' => $user->id,
                'speciality_id' => $request->speciality_id,
                'hourly_rate' => $request->hourly_rate,
            ]);
            return redirect()->route('users.index')->with('success', 'Doctor created successfully');
        }



      
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        $specialities = Speciality::all();
        return view('users.edit', compact('user','specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $user = User::find($id);

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
               return redirect()->back()->with('error', 'Patient not found');
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
                return redirect()->back()->with('error', 'Doctor not found');
            }
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Auth::user()->id == $id){
            return redirect()->back()->with('error', 'Admin cannot be deleted');
        }
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
