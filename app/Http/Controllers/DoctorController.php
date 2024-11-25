<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
    public function show(Doctor $doctor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
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
