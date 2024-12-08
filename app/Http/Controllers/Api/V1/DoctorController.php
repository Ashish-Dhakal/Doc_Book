<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\V1\BaseController;

class DoctorController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
       $validate = Validator::make($request->all(), [
           'f_name' => ['required', 'string', 'max:255'],
           'l_name' => ['required', 'string', 'max:255'],
           'phone' => ['required', 'numeric' , 'digits: 10'],
           'address' => ['required', 'string', 'max:255'],
           'age' => ['required', 'numeric'],
           'gender' => ['required', 'string'],
           'blood_group' => ['required', 'string'],
           'hourly_rate' => ['required', 'numeric'],
           'password' => ['required', 'string', 'min:8', 'confirmed'],
       ]);

       if ($validate->fails()) {
           return response()->json([
               'status' => false,
               'message' => 'Validation failed',
               'errors' => $validate->errors()->all(),
           ]);
       }
    
       $user = Auth::user()->id;
       $user = User::find($user);
       $user->update([
           'f_name' => $request->f_name,
           'l_name' => $request->l_name,
           'phone' => $request->phone,
           'address' => $request->address,
           'password' => $request->password,
           'age' => $request->age,
           'gender' => $request->gender,
           'blood_group' => $request->blood_group,
       ]);

       $user->doctor()->update([
           'hourly_rate' => $request->hourly_rate,
       ]);


       return $this->successResponse($user, 'Your profile updated successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
    public function search(Request $request)
    {
        $query = $request->get('query');
    
        $doctors = Doctor::with(['user', 'speciality', 'appointmentSlots'])
            ->whereHas('user', function ($q) use ($query) {
                $q->where('f_name', 'like', "%$query%")
                  ->orWhere('l_name', 'like', "%$query%");
            })
            ->get();
    
        return response()->json(['doctors' => $doctors]);
    }
}
