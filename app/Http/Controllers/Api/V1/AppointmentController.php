<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Speciality;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\PatientHistory;
use App\Models\AppointmentSlot;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;



class AppointmentController extends BaseController
{
    use AuthorizesRequests;
    /**
     * Fetch all appointments
     */
    public function index()
    {
        $this->authorize('viewAny', Appointment::class);


        $userId = Auth::user()->id;
        $patient = Patient::where('user_id', $userId)->first();
        $doctorId = Doctor::where('user_id', $userId)->first();
        $data['specialities'] = Speciality::all();
        // dd($specialities);

        if ($patient) {
            $data['appointments'] = Appointment::where('patient_id', $patient->id)->paginate(5);
        } elseif (Auth::user()->roles == 'admin') {
            $data['appointments'] = Appointment::paginate(5);
        } elseif (Auth::user()->roles == 'doctor') {
            $data['appointments'] = Appointment::where('doctor_id', $doctorId->id)
                ->where('status', 'booked')->get();
        } else {
            return redirect()->route('login');
        }

        // return view('appointments.index', $data);
        return $this->successResponse($data, 'Appointments retrieved successfully');
    }

    /**
     * Create appointment
     */
    public function store(Request $request)
    {
        // dd($request->toArray());

        $validateAppointment = Validator::make($request->all(), [
            'doctor_id' => ['required'],
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'patient_id' => ['nullable', 'exists:patients,id', function ($attribute, $value, $fail) {
                // Only apply the validation if the logged-in user is an admin
                if (Auth::user()->roles === 'admin' && empty($value)) {
                    // $fail('The patient ID is required when submitting as admin.');

                    return $this->errorResponse('The patient ID is required when submitting as admin.');
                }
            }],
        ]);

        if ($validateAppointment->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validateAppointment->errors()->all(),
            ], 422); // 422 is commonly used for validation errors
        }

        // Get the input times (24-hour format)
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Convert the times to Carbon instances (don't format to 12-hour yet)
        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);


        if ($endTimeCarbon->lt($startTimeCarbon)) {
            // return redirect()->back()->with('error', 'End time should be after start time.');

            return $this->errorResponse('End time should be after start time.');
        }

        // check if the doctor is availabelin that time or not
        $doctorId = $request->input('doctor_id');
        $date = $request->input('date');

        $conflict = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->whereIn('status', ['pending', 'booked'])
            ->exists();


        if ($conflict) {

            return $this->errorResponse('The selected time slot is already booked for this doctor.');
        }


        $appointmentSlots = AppointmentSlot::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($subQuery) use ($startTime, $endTime) {
                    // Check if the time slot is either booked or unavailable
                    $subQuery->whereIn('status', ['unavailable', 'booked'])
                        ->where(function ($timeQuery) use ($startTime, $endTime) {
                            // Check for overlapping times
                            $timeQuery->where('start_time', '<', $endTime)
                                ->where('end_time', '>', $startTime);
                        });
                });
            })
            ->exists();
        if ($appointmentSlots) {
            return $this->errorResponse('Doctor is not available at that time slot.');
        }



        if (Auth::user()->roles == 'patient') {
            $userId = Auth::user()->id;
            $patient = Patient::where('user_id', $userId)->first();
            $patientId = $patient->id;
            $appointment = new Appointment();
            $appointment->patient_id = $patientId;
            $appointment->doctor_id = $request->input('doctor_id');
            $appointment->date = $request->input('date');
            $appointment->start_time = $request->input('start_time');
            $appointment->end_time = $request->input('end_time');



            // Send email to both the patient and the doctor
            $doctor = Doctor::find($appointment->doctor_id);
            $patientEmail = $patient->user->email;
            $doctorEmail = $doctor->user->email;

            // // Send email to patient
            // Mail::to($patientEmail)
            //     ->send(new AppointmentMail($appointment, 'patient'));

            // // Send email to doctor 
            // Mail::to($doctorEmail)
            //     ->send(new AppointmentMail($appointment, 'doctor'));


            $appointment->save();
            return $this->successResponse($appointment, 'Appointment created successfully');
        } elseif (Auth::user()->roles == 'admin') {
            $appointment = new Appointment();
            $appointment->patient_id = $request->input('patient_id');
            $appointment->doctor_id = $request->input('doctor_id');
            $appointment->date = $request->input('date');
            $appointment->start_time = $request->input('start_time');
            $appointment->end_time = $request->input('end_time');
            $appointment->status = 'booked';
            $appointment->save();

            // Send email to both the patient and the doctor
            $patient = Patient::find($appointment->patient_id);
            $doctor = Doctor::find($appointment->doctor_id);
            $patientEmail = $patient->user->email;
            $doctorEmail = $doctor->user->email;

            // // Send email to patient 
            // Mail::to($patientEmail)
            //     ->send(new AppointmentMail($appointment, 'patient'));

            // // Send email to doctor 
            // Mail::to($doctorEmail)
            //     ->send(new AppointmentMail($appointment, 'doctor'));


            // dd($request->input('doctor_id'));
            $appointmentSlot = new AppointmentSlot();
            $appointmentSlot->doctor_id = $request->input('doctor_id');
            $appointmentSlot->date = $request->input('date');
            $appointmentSlot->start_time = $request->input('start_time');
            $appointmentSlot->end_time = $request->input('end_time');
            $appointmentSlot->status = 'booked';
            $appointmentSlot->save();

            // return redirect()->route('appointments.index')->with('success', 'Appointment created successfully');

            return $this->successResponse($appointment, 'Appointment created successfully');
        } else {

            return $this->errorResponse('You are not authorized to create an appointment.');
        }
    }


    /**
     * Display appointment.
     */
    public function show(string $id)
    {
        $appointment = Appointment::with('reviews')
            ->with('patient.user:id,f_name,l_name')->with('doctor.user:id,f_name,l_name')->find($id);

        if (!$appointment) {
            return $this->errorResponse('Appointment not found');
        }

        // Authorize the specific appointment instance
        $this->authorize('view', $appointment);

        // return view('appointments.show', compact('appointment'));
        return $this->successResponse($appointment, 'Appointment retrieved successfully');
    }

    /**
     * Update appointmnent  
     */
    public function update(Request $request, Appointment $appointment)
    {
        if ($appointment->status == 'completed' || $appointment->status == 'booked') {
            return $this->errorResponse('This appointment is already completed or booked and cannot be modified.', 422);
        }

        if (!$this->authorize('edit', $appointment)) {
            return $this->errorResponse('You are not authorized to update this appointment.');
        }

        // Validate the incoming request
        $validateAppointment = Validator::make($request->all(), [
            'doctor_id' => ['required'],
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'patient_id' => ['nullable', function ($attribute, $value, $fail) {
                // Only apply the validation if the logged-in user is an admin
                if (Auth::user()->roles === 'admin' && empty($value)) {
                    return $this->errorResponse('The patient ID is required when submitting as admin.');
                }
            }],
        ]);

        // Check if validation fails
        if ($validateAppointment->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validateAppointment->errors()->all(),
            ], 422);
        }

        // Get the input times (24-hour format)
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Convert the times to Carbon instances (don't format to 12-hour yet)
        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);

        // Check if end time is after start time
        if ($endTimeCarbon->lt($startTimeCarbon)) {
            return $this->errorResponse('End time should be after start time.');
        }

        // Check if the doctor has any unavailable slots or existing appointments at the given time and date
        $doctorId = $request->input('doctor_id');
        $date = $request->input('date');

        // Check if there is an existing appointment or unavailable slot for the doctor at the selected time
        $appointmentConflict = AppointmentSlot::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('status', 'booked')
                    ->orWhere('status', 'unavailable')
                    ->where(function ($timeQuery) use ($startTime, $endTime) {
                        $timeQuery->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    });
            })
            ->exists();

        if ($appointmentConflict) {
            return $this->errorResponse('The doctor has an existing appointment or unavailable slot at this time.');
        }

        // Check if the doctor is available at the selected time slot for the update
        $conflict = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->where('status', ['pending', 'booked'])
            ->where('id', '!=', $appointment->id) // Exclude the current appointment from conflict check
            ->exists();

        if ($conflict) {
            return $this->errorResponse('The selected time slot is already booked for this doctor.');
        }

        // Check if the doctor has any unavailable slots during the new time range
        $appointmentSlots = AppointmentSlot::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('status', 'unavailable')
                    ->where(function ($timeQuery) use ($startTime, $endTime) {
                        $timeQuery->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    });
            })
            ->exists();

        if ($appointmentSlots) {
            return $this->errorResponse('Doctor is not available at the selected time.');
        }

        // Now update the appointment
        $appointment->doctor_id = $request->input('doctor_id');
        $appointment->date = $request->input('date');
        $appointment->start_time = $request->input('start_time');
        $appointment->end_time = $request->input('end_time');

        // If logged in as patient, set the patient ID if not set yet (in case it's not passed in the request)
        if (Auth::user()->roles == 'patient' && !$appointment->patient_id) {
            $userId = Auth::user()->id;
            $patient = Patient::where('user_id', $userId)->first();
            $appointment->patient_id = $patient->id;
        }

        // Handle role-specific behavior (admin or patient)
        if (Auth::user()->roles == 'admin') {
            // If updated by admin, we explicitly set the patient_id and mark the appointment as 'booked'
            $appointment->patient_id = $request->input('patient_id');
            $appointment->status = 'booked';
        }

        // Save the updated appointment
        $appointment->save();

        // Send email to both the patient and the doctor
        $patient = Patient::find($appointment->patient_id);
        $doctor = Doctor::find($appointment->doctor_id);
        $patientEmail = $patient->user->email;
        $doctorEmail = $doctor->user->email;

        // Uncomment below to send emails (Mail::to is just for illustration)
        // Mail::to($patientEmail)
        //     ->send(new AppointmentMail($appointment, 'patient'));

        // Mail::to($doctorEmail)
        //     ->send(new AppointmentMail($appointment, 'doctor'));

        // Return success response with updated appointment
        return $this->successResponse($appointment, 'Appointment updated successfully');
    }

    /**
     * Delete appointment
     */
    public function destroy(string $id)
    {
        // Find the appointment by its ID
        $appointment = Appointment::find($id);

        // Check if the appointment exists
        if (!$appointment) {
            return $this->errorResponse('Appointment not found', 404);
        }

        // Delete the appointment
        $appointment->delete();

        // Return a success response
        return $this->successResponse(null, 'Appointment deleted successfully');
    }


    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, Appointment $appointment, AppointmentSlot $appointmentSlot)
    {
        $request->validate([
            'status' => 'required|in:pending,booked,rescheduled,cancelled,completed',
        ]);
        $status = $request->status;

        if ($appointment->status === 'completed' && $request->status !== 'completed') {
            // return redirect()->route('appointments.index')->with('error', 'This appointment is already completed and cannot be modified.');

            return $this->errorResponse('This appointment is already completed and cannot be modified.');
        }

        switch ($status) {
            case 'pending':
                $appointment->status = 'pending';

                $appointmentInfo = AppointmentSlot::where('doctor_id', $appointment->doctor_id)
                    ->where('date', $appointment->date)
                    ->where('start_time', $appointment->start_time)
                    ->where('end_time', $appointment->end_time)
                    ->where('status', 'booked')
                    ->first();
                if ($appointmentInfo) {
                    // return redirect()->route('appointments.index')->with('error', 'This time slot is already booked');
                    return $this->errorResponse('This time slot is already booked');
                }
                break;
            case 'booked':
                $appointment->status = 'booked';
                $appointmentSlot->status = 'booked';
                $appointmentSlot->start_time = $appointment->start_time;
                $appointmentSlot->doctor_id = $appointment->doctor_id;
                $appointmentSlot->end_time = $appointment->end_time;
                $appointmentSlot->date = $appointment->date;

                $doctorInfo = AppointmentSlot::where('doctor_id', $appointment->doctor_id)
                    ->where('date', $appointment->date)
                    ->where('start_time', $appointment->start_time)
                    ->where('end_time', $appointment->end_time)
                    ->where('status', 'booked')
                    ->first();
                if ($doctorInfo) {
                    // return redirect()->route('appointments.index')->with('error', 'This time slot is already booked');

                    return $this->errorResponse('This time slot is already booked');
                } else {
                    $appointmentSlot->save();
                }
                break;
            case 'rescheduled':
                $appointment->status = 'rescheduled';
                break;
            case 'cancelled':
                $appointment->status = 'cancelled';

                $appointmentSlot = AppointmentSlot::where('doctor_id', $appointment->doctor_id)
                    ->where('date', $appointment->date)
                    ->where('start_time', $appointment->start_time)
                    ->where('end_time', $appointment->end_time)
                    ->first();

                if ($appointmentSlot) {
                    $appointmentSlot->delete();
                }

                $appointment->delete();

                // return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully');

                return $this->successResponse(null, 'Appointment cancelled successfully');
                break;

            case 'completed':
                if ($appointment->status !== 'completed') {
                    // Mark the appointment as completed
                    $appointment->status = 'completed';

                    // Delete the corresponding appointment slot
                    AppointmentSlot::where('doctor_id', $appointment->doctor_id)
                        ->where('date', $appointment->date)
                        ->where('start_time', $appointment->start_time)
                        ->where('end_time', $appointment->end_time)
                        ->delete();
                }
                if (!$appointment->reviews) {
                    // return back()->with('error', 'Please add review first');
                    return $this->errorResponse('Please add review first');
                }

                // Example start time and end time
                $startTime = Carbon::parse($appointment->start_time);  // Doctor's shift start time
                $endTime = Carbon::parse($appointment->end_time);    // Doctor's shift end time

                // Calculate the duration in hours
                $durationInHours = $startTime->diffInHours($endTime);

                // Calculate the total fee
                $totalFee = $durationInHours * $appointment->doctor->hourly_rate;

                $payment = Payment::create([
                    'appointment_id' => $appointment->id,
                    'amount' => $totalFee,
                    'patient_id' => $appointment->patient_id,
                ]);

                $firstReview = $appointment->reviews->first();
                if (!$firstReview) {
                    // return back()->with('error', 'Doctor is yet to add review first');
                    return $this->errorResponse('Doctor is yet to add review first');
                }

                PatientHistory::create([
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $appointment->doctor_id,
                    'review_id' => $firstReview->id,
                    'payment_id' => $payment->id,
                ]);

                $patient = Patient::find($appointment->patient_id);
                $patientEmail = $patient->user->email;
                // Mail::to($patientEmail)->send(new AppointmentCompleteMail($appointment));

                // After completing, redirect with a flag to show the review modal
                $appointment->save();
                // return redirect()->route('appointments.index')->with('success', 'Appointment marked as completed')->with('showReviewModal', true);

                return $this->successResponse(null, 'Appointment marked as completed');
                break;
            default:
                $appointment->status = 'pending';
        }
        $appointment->save();
        // return redirect()->route('appointments.index')->with('success', 'Appointment status updated successfully');
        return $this->successResponse(null, 'Appointment status updated successfully');
    }
}
