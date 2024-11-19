<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest\UserCreateRequest;
use App\Http\Requests\UserRequest\UserUpdateRequest;
use App\Models\Doctor;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['users'] = User::all();
        return view('users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request)
    {
        // $validated = $request->validate([
        //     'f_name' => 'required',
        //     'l_name' => 'required',
        //     'phone' => 'required',
        //     'address' => 'required',
        //     'email' => 'required|email|unique:users',
        //     'roles' => 'required',
        //     'password' => 'required|min:8',
        //     ]);

        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'roles' => $request->roles,
            'password' => bcrypt($request->password),
        ]);

        if ($user->roles == 'patient') {
            Patient::create([
                'user_id' => $user->id,
            ]);
        }
        if ($user->roles == 'doctor') {
            Doctor::create([
                'user_id' => $user->id,
            ]);
        }



        return redirect()->route('users.index')->with('success', 'User created successfully');
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
        return view('users.edit', compact('user'));
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
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
