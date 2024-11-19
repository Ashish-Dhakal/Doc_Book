<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest\UserCreateRequest;
use App\Http\Requests\UserRequest\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;

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

        User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'roles' => $request->roles,
            'password' => bcrypt($request->password),
        ]);
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
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
    }
}
