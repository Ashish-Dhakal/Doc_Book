<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Support\Facades\Validator;

use function Pest\Laravel\json;

class AppointmentSlotController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->roles == 'doctor') {
            $userId = Auth::user()->id;
            $doctor = Doctor::where('user_id', $userId)->first();
            $data['appointmentSlots'] = AppointmentSlot::with('doctor')
                ->where('doctor_id', $doctor->id);
            // ->paginate(5);
            return $this->successResponse($data, 'Appointment Slots retrieved successfully');
        } else {
            $data['appointmentSlots'] = AppointmentSlot::get();
            if ($data['appointmentSlots']->isEmpty()) {
                return $this->errorResponse('No appointment slots found', ' no appointment slots found', 404);
            }
            return $this->successResponse($data, 'Appointment Slots retrieved successfully');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validated = Validator::make($request->all(), [
            'doctor_id' => 'required|numeric',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'status' => 'required|string',
        ]);

        // Check if validation failed
        if ($validated->fails()) {
            return $this->errorResponse('Validation failed', $validated->errors()->all());
        }

        // Get validated input
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);

        // Check if end time is after start time
        if ($endTimeCarbon->lte($startTimeCarbon)) {
            return $this->errorResponse('End time must be after start time.', 400);
        }

        // Check for conflicting schedules
        $conflictingSchedule = AppointmentSlot::where('doctor_id', $request->input('doctor_id'))
            ->whereDate('date', $request->input('date')) // Check for the same date
            ->where(function ($query) use ($startTimeCarbon, $endTimeCarbon) {
                $query->whereBetween('start_time', [$startTimeCarbon, $endTimeCarbon])
                    ->orWhereBetween('end_time', [$startTimeCarbon, $endTimeCarbon])
                    ->orWhere(function ($query) use ($startTimeCarbon, $endTimeCarbon) {
                        // Full overlap: Existing schedule fully covers the new time range
                        $query->where('start_time', '<', $startTimeCarbon)
                            ->where('end_time', '>', $endTimeCarbon);
                    });
            })
            ->exists();

        // If there's a conflict, return error response
        if ($conflictingSchedule) {
            return $this->errorResponse('The requested time slot conflicts with an existing schedule for this doctor.', 400);
        }

        // Create the new appointment slot
        $appointmentSlot = new AppointmentSlot();
        $appointmentSlot->doctor_id = $request->input('doctor_id');
        $appointmentSlot->date = $request->input('date');
        $appointmentSlot->start_time = $startTime; // Store the raw start time
        $appointmentSlot->end_time = $endTime;     // Store the raw end time
        $appointmentSlot->status = $request->input('status');
        $appointmentSlot->save();

        return $this->successResponse($appointmentSlot, 'Appointment Slot created successfully');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointmentSlot = AppointmentSlot::find($id);
        if (!$appointmentSlot) {
            return $this->errorResponse('Appointment Slot not found', 404);
        }
        return $this->successResponse($appointmentSlot, 'Appointment Slot retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $appointmentSlot = AppointmentSlot::find($id);
        if (!$appointmentSlot) {
            return $this->errorResponse('Appointment Slot not found', 404);
        }

        // Validate the incoming data
        $validated = Validator::make($request->all(), [
            'doctor_id' => 'required|numeric',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'status' => 'required|string',
        ]);

        // Check if validation failed
        if ($validated->fails()) {
            return $this->errorResponse('Validation failed', $validated->errors()->all());
        }

        // Get validated input
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);

        // Check if end time is after start time
        if ($endTimeCarbon->lte($startTimeCarbon)) {
            return $this->errorResponse('End time must be after start time.', 400);
        }


        // Update the appointment slot
        $appointmentSlot->update($request->all());
        return $this->successResponse($appointmentSlot, 'Appointment Slot updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointmentSlot = AppointmentSlot::find($id);
        if (!$appointmentSlot) {
            return $this->errorResponse('Appointment Slot not found', 404);
        }
        $appointmentSlot->delete();
        return $this->successResponse(null, 'Appointment Slot deleted successfully');
    }
}
