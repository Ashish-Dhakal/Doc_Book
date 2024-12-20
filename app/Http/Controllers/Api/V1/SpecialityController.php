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
     * Fetch all specialities
     */
    public function index()
    {
        $data['specialities'] = Speciality::all()
            ->map(function ($speciality) {
                return [
                    'id' => $speciality->id,
                    'name' => $speciality->name,
                ];
            });

        return $this->successResponse($data, 'speciality list');
    }

    /**
     * Create a new speciality
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
        // Fetch the speciality along with its associated doctors
        $speciality = Speciality::with('doctors.user')->find($id);
        // Map doctors to the desired structure
        $doctors = $speciality->doctors->map(function ($doctor) {
            return [
                'id' => $doctor->user->id,
                'First name' => $doctor->user->f_name,
                'Last name' => $doctor->user->l_name,
                'email' => $doctor->user->email,
                'phone' => $doctor->user->phone,
                'hourly_rate' => $doctor->hourly_rate,
            ];
        });
        // Prepare the response data
        $data = [
            'specialization_name' => $speciality->name,
            'doctors' => $doctors,
        ];

        // Return success response
        return $this->successResponse($data, 'Speciality details');
    }

    /**
     * Update the speciality
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
     * Delete speciality
     */
    public function destroy(string $id)
    {
        $specialization = Speciality::find($id);
        $specialization->delete();
        // return redirect()->route('specializations.index')->with('success', 'Speciality deleted successfully');
        return $this->successResponse([], 'Speciality deleted successfully');
    }
}
