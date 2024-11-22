<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Speciality;
use Illuminate\Http\Request;

class SpecialityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['specialities'] = Speciality::all();
        $data['doctors'] = Doctor::all();
        return view('specializations.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('specializations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20',
        ]);
        Speciality::create($validated);
        return redirect()->route('specializations.index')->with('success', 'Speciality created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data['specialization'] = Speciality::find($id);
        $data['doctors'] = Doctor::where('speciality_id', $id)->get();
        return view('specializations.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $specialization = Speciality::find($id);
        return view('specializations.edit', compact('specialization'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20',
        ]);
        $specialization = Speciality::find($id);
        $specialization->update($validated);
        return redirect()->route('specializations.index')->with('success', 'Speciality updated successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $specialization = Speciality::find($id);
        $specialization->delete();
        return redirect()->route('specializations.index')->with('success', 'Speciality deleted successfully');
        
    }

}
