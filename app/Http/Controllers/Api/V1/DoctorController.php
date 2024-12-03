<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    public function update(Request $request, string $id)
    {
        //
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
