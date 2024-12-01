<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Doctor;
use App\Models\Speciality;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\V1\BaseController;


class SpecialityController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['specialities'] = Speciality::all();

        return $this->successResponse($data, 'speciality list');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
        ]);

        if ($validated->fails()) {
            return $this->errorResponse('Validation failed', $validated->errors()->all());
        }

        $data['speciality'] = Speciality::create([
            'name' => $request->name
        ]);

        return $this->successResponse($data, 'Speciality created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $data['specialization'] = Speciality::find($id);
        $data['doctors'] = Doctor::where('speciality_id', $id)->get();
        return $this->successResponse($data, 'Speciality details');
        // return view('specializations.show', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20',
        ]);
        $specialization = Speciality::find($id);
        $specialization->update($validated);
        return $this->successResponse($specialization, 'Speciality updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $specialization = Speciality::find($id);
        $specialization->delete();
        // return redirect()->route('specializations.index')->with('success', 'Speciality deleted successfully');
        return $this->successResponse([], 'Speciality deleted successfully');
    }
}
