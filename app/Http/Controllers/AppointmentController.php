<?php

namespace App\Http\Controllers;

use App\Helper\AppointmentHelper;
use App\Mail\AppointmentCompleteMail;
use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Speciality;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Mail\AppointmentMail;
use App\Models\PatientHistory;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class AppointmentController extends Controller
{
    use AuthorizesRequests;

    protected $appointmentHelper;

    public function __construct(AppointmentHelper $appointmentHelper)
    {
        // Store the instance in the property
        $this->appointmentHelper = $appointmentHelper;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Appointment::class);

        $data = $this->appointmentHelper->getAppointments();

        // Redirect if no appointments
        if (empty($data['appointments']) || $data['appointments']->isEmpty()) {
            return redirect()->route('login')->withErrors('No appointments found.');
        }

        return view('appointments.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $userRole = Auth::user()->roles;

        if ($userRole == 'admin') {
            $data['patients'] = Patient::all();
        } elseif ($userRole == 'doctor') {
            $this->authorize('create', Appointment::class);
        }

        $doctors = Doctor::where('speciality_id', $request->speciality_id)->get();

        if ($doctors->isEmpty()) {
            return redirect()->back()->with('error', 'No doctors available for this speciality');
        }

        $data['doctors'] = $doctors;
        // $data['doctors'] = Doctor::where('speciality_id', $request->speciality_id)->get();

        return view('appointments.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AppointmentHelper $appointmentHelper)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'patient_id' => ['nullable', 'exists:patients,id', function ($attribute, $value, $fail) {
                if (Auth::user()->roles === 'admin' && empty($value)) {
                    $fail('The patient ID is required when submitting as admin.');
                }
            }],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->all());
        }

        // Validate times
        $timeError = $appointmentHelper->validateTime($request->start_time, $request->end_time);
        if ($timeError) {
            return redirect()->back()->with('error', $timeError);
        }

        // Check doctor availability
        $availabilityError = $appointmentHelper->checkDoctorAvailability(
            $request->doctor_id,
            $request->date,
            $request->start_time,
            $request->end_time
        );

        if ($availabilityError) {
            return redirect()->back()->with('error', $availabilityError);
        }

        $appointmentData = [
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'booked',
        ];

        if (Auth::user()->roles === 'patient') {
            $patient = Patient::where('user_id', Auth::id())->first();
            $appointmentData['patient_id'] = $patient->id;
        } else if (Auth::user()->roles === 'admin') {
            $appointmentData['patient_id'] = $request->patient_id;
            $appointmentData['slot'] = true;
        } else {
            abort(403, 'Unauthorized');
        }

        $appointmentHelper->createAppointment($appointmentData);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $appointment = Appointment::with('reviews')->find($id);
        // $appointment = Appointment::with('reviews')->find($id);


        if (!$appointment) {
            abort(404);
        }

        // Authorize the specific appointment instance
        $this->authorize('view', $appointment);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $appointment = Appointment::find($id);
        if ($appointment->status == 'completed' || $appointment->status == 'booked') {
            return redirect()->route('appointments.index')->with('error', 'This appointment is already completed and cannot be modified.');
        }

        if (!$appointment) {
            abort(404, 'Appointment not found');
        }
        $this->authorize('edit', $appointment);

        $data['doctors'] = Doctor::all();
        $data['appointment'] = $appointment;

        return view('appointments.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'doctor_id' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'patient_id' => ['nullable', function ($attribute, $value, $fail) {
                // Only apply the validation if the logged-in user is an admin
                if (Auth::user()->roles === 'admin' && empty($value)) {
                    $fail('The patient ID is required when submitting as admin.');
                }
            }],
        ]);

        // Validate if the doctor and patient IDs exist
        $doctor = Doctor::find($request->doctor_id);
        $patient = Patient::find($request->patient_id);
        if (empty($doctor) || (Auth::user()->roles !== 'admin' && empty($patient))) {
            return redirect()->back()->with('error', 'Invalid doctor id or patient id');
        }

        // Get the input times (24-hour format)
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Convert the times to Carbon instances (don't format to 12-hour yet)
        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);

        // Check if the end time is after the start time
        if ($endTimeCarbon->lt($startTimeCarbon)) {
            return redirect()->back()->with('error', 'End time should be after start time.');
        }

        // Check if the doctor is available for the new time slot
        $doctorId = $request->input('doctor_id');
        $date = $request->input('date');

        // Check if the new date and time conflict with existing appointments (pending/booked)
        $conflict = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('id', '!=', $appointment->id) // Exclude the current appointment from the conflict check
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
            return redirect()->back()->with('error', 'The selected time slot is already booked for this doctor.');
        }

        // Check if the doctor has an existing appointment slot that conflicts with the new time
        $appointmentSlotConflict = AppointmentSlot::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('id', '!=', $appointment->appointmentSlot->id) // Exclude current appointment slot
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($subQuery) use ($startTime, $endTime) {
                    $subQuery->where('status', ['unavailable', 'booked'])
                        ->where(function ($timeQuery) use ($startTime, $endTime) {
                            $timeQuery->where('start_time', '<', $endTime)
                                ->where('end_time', '>', $startTime);
                        });
                });
            })
            ->exists();

        if ($appointmentSlotConflict) {
            return redirect()->back()->with('error', 'Doctor is not available at that time.');
        }

        // Proceed to update the appointment
        if (Auth::user()->roles == 'patient') {
            $userId = Auth::user()->id;
            $patient = Patient::where('user_id', $userId)->first();
            $patientId = $patient->id;
            $appointment->patient_id = $patientId;
            $appointment->doctor_id = $request->input('doctor_id');
            $appointment->date = $request->input('date');
            $appointment->start_time = $request->input('start_time');
            $appointment->end_time = $request->input('end_time');

            // Send email to both the patient and the doctor
            $doctor = Doctor::find($appointment->doctor_id);
            $patientEmail = $patient->user->email;
            $doctorEmail = $doctor->user->email;

            // Send email to patient
            // Mail::to($patientEmail)
            //     ->send(new AppointmentMail($appointment, 'patient'));

            // Send email to doctor (cc)
            // Mail::to($doctorEmail)
            //     ->send(new AppointmentMail($appointment, 'doctor'));

            $appointment->save();
            return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully');
        } elseif (Auth::user()->roles == 'admin') {
            // Admin is updating the appointment
            $appointment->patient_id = $request->input('patient_id');
            $appointment->doctor_id = $request->input('doctor_id');
            $appointment->date = $request->input('date');
            $appointment->start_time = $request->input('start_time');
            $appointment->end_time = $request->input('end_time');
            $appointment->status = 'booked'; // or 'pending' depending on the status you want to set
            $appointment->save();

            // Send email to both the patient and the doctor
            $patient = Patient::find($appointment->patient_id);
            $doctor = Doctor::find($appointment->doctor_id);
            $patientEmail = $patient->user->email;
            $doctorEmail = $doctor->user->email;

            // Send email to patient
            // Mail::to($patientEmail)
            //     ->send(new AppointmentMail($appointment, 'patient'));

            // Send email to doctor (cc)
            // Mail::to($doctorEmail)
            //     ->send(new AppointmentMail($appointment, 'doctor'));

            // Update the appointment slot
            $appointmentSlot = $appointment->appointmentSlot;
            $appointmentSlot->doctor_id = $request->input('doctor_id');
            $appointmentSlot->date = $request->input('date');
            $appointmentSlot->start_time = $request->input('start_time');
            $appointmentSlot->end_time = $request->input('end_time');
            $appointmentSlot->status = 'booked'; // or 'unavailable', depending on your logic
            $appointmentSlot->save();

            return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully');
        } else {
            abort(403);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }


    public function updateStatus(Request $request, Appointment $appointment, AppointmentSlot $appointmentSlot)
    {
        $request->validate([
            'status' => 'required|in:pending,booked ,cancelled,completed',
        ]);

        $status = $request->status;

        // Check if the appointment is already completed and cannot be modified
        if ($appointment->status === 'completed' && $request->status !== 'completed') {
            return redirect()->route('appointments.index')->with('error', 'This appointment is already completed and cannot be modified.');
        }

        // Call the appropriate method in AppointmentHelper based on the status
        switch ($status) {
            case 'pending':
                $response = $this->appointmentHelper->handlePending($appointment, $status, $appointmentSlot);
                break;

            case 'booked':
                $response = $this->appointmentHelper->handleBooked($appointment, $appointmentSlot, $status);
                break;

            case 'cancelled':
                $response = $this->appointmentHelper->handleCancelled($appointment, $appointmentSlot, $status);
                break;

            case 'completed':
                $response = $this->appointmentHelper->handleCompleted($appointment, $status);
                break;

            default:
                $appointment->status = 'pending';
        }

        // If there's a response (error), return it
        if ($response) {
            return redirect()->route('appointments.index')->with('error', $response);
        }

        // Save the appointment after status change
        $appointment->save();
        return redirect()->route('appointments.index')->with('success', 'Appointment status updated successfully');
    }
}
