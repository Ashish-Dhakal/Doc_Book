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
     * Fetch all appointment slots
     */
    public function index()
    {
        if (Auth::user()->roles == 'doctor') {
            $userId = Auth::user()->id;
            $doctor = Doctor::where('user_id', $userId)->first();

            if (!$doctor) {
                return $this->errorResponse('Doctor not found', 404);
            }

            // Fetch appointment slots for the doctor
            $appointmentSlots = AppointmentSlot::with('doctor.user')
                ->where('doctor_id', $doctor->id)
                ->get(); // Fetch the data to get a collection

            // Transform the data using map
            $data['appointmentSlots'] = $appointmentSlots->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'date' => $slot->date,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'status' => $slot->status,
                ];
            });

            return $this->successResponse($data, 'Appointment Slots retrieved successfully');
        } else {
            $appointmentSlots = AppointmentSlot::with('doctor.user')->get();
            $data['appointmentSlots'] = $appointmentSlots->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'doctor_id' => $slot->doctor_id,
                    'doctor_name' => $slot->doctor->user->f_name . ' ' . $slot->doctor->user->l_name,
                    'date' => $slot->date,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'status' => $slot->status,
                ];
            });

            if ($data['appointmentSlots']->isEmpty()) {
                return $this->errorResponse('No appointment slots found', ' no appointment slots found', 404);
            }
            return $this->successResponse($data, 'Appointment Slots retrieved successfully');
        }
    }

    /**
     * Create a new appointment slot
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validated = Validator::make($request->all(), [
            'doctor_id' => ['required', 'exists:doctors,id', function ($attribute, $value, $fail) use ($request) {
                // Only validate doctor_id if status is 'doctor'
                if (Auth::user()->roles == 'admin' && empty($value)) {
                    $fail($attribute . ' is required for admin.');
                }
            }],
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'status' => 'required|string',
        ]);


        // dd($request->doctor_id);
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

        if (Auth::user()->roles == 'admin') {
            $doctor_id = $request->input('doctor_id');
            $doctor = Doctor::where('user_id', $doctor_id)->first();
            if (!$doctor) {
                return $this->errorResponse('Doctor not found', 404);
            }
        } else {
            $userId = Auth::user()->id;
            $doctor = Doctor::where('user_id', $userId)->first();
            $doctor_id = $doctor->id;
        }

        // Check for conflicting schedules
        $conflictingSchedule = AppointmentSlot::where('doctor_id', $doctor_id)
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
            return $this->errorResponse('The requested time slot already booked for this doctor.', 400);
        }

        // Create the new appointment slot
        $appointmentSlot = new AppointmentSlot();
        $appointmentSlot->doctor_id = $doctor_id;
        $appointmentSlot->date = $request->input('date');
        $appointmentSlot->start_time = $startTime; // Store the raw start time
        $appointmentSlot->end_time = $endTime;     // Store the raw end time
        $appointmentSlot->status = $request->input('status');
        $appointmentSlot->save();

        return $this->successResponse($appointmentSlot, 'Appointment Slot created successfully');
    }



    /**
     * Display appointment slot.
     */
    public function show(string $id)
    {
        $appointmentSlot = AppointmentSlot::find($id);
        $slot_data = [
            'id' => $appointmentSlot->id,
            'doctor_id' => $appointmentSlot->doctor_id,
            'doctor f_name' => $appointmentSlot->doctor->user->f_name,
            'doctor l_name' => $appointmentSlot->doctor->user->l_name,
            'date' => $appointmentSlot->date,
            'start_time' => $appointmentSlot->start_time,
            'end_time' => $appointmentSlot->end_time,
            'status' => $appointmentSlot->status,
        ];
        if (!$appointmentSlot) {
            return $this->errorResponse('Appointment Slot not found', 404);
        }
        return $this->successResponse($slot_data, 'Appointment Slot retrieved successfully');
    }

    /**
     * Update appointment slot
     */
    public function update(Request $request, string $id)
    {
        $appointmentSlot = AppointmentSlot::find($id);
        if (!$appointmentSlot) {
            return $this->errorResponse('Appointment Slot not found', 404);
        }

        // Validate the incoming data
        $validated = Validator::make($request->all(), [
            'doctor_id' => ['required', 'exists:doctors,id', function ($attribute, $value, $fail) use ($request) {
                // Only validate doctor_id if status is 'doctor'
                if (Auth::user()->roles == 'admin' && empty($value)) {
                    $fail($attribute . ' is required for admin.');
                }
            }],
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

        if (Auth::user()->roles == 'admin') {
            $doctor_id = $request->input('doctor_id');
            $doctor = Doctor::where('user_id', $doctor_id)->first();
            if (!$doctor) {
                return $this->errorResponse('Doctor not found', 404);
            }
        } else {
            $userId = Auth::user()->id;
            $doctor = Doctor::where('user_id', $userId)->first();
            $doctor_id = $doctor->id;
        }

        // Check for conflicting schedules
        $conflictingSchedule = AppointmentSlot::where('doctor_id', $doctor_id)
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
            return $this->errorResponse('The requested time slot already booked for this doctor.', 400);
        }


        // Update the appointment slot
        $appointmentSlot->update($request->all());
        return $this->successResponse($appointmentSlot, 'Appointment Slot updated successfully');
    }

    /**
     * Delete appointment slot
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
