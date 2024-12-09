<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Speciality;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Mail\AppointmentMail;
use App\Models\PatientHistory;
use App\Models\AppointmentSlot;
use App\Helper\AppointmentHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AppointmentController extends BaseController
{
    use AuthorizesRequests;

    protected $appointmentHelper;

    public function __construct(AppointmentHelper $appointmentHelper)
    {
        $this->appointmentHelper = $appointmentHelper;
    }

    /**
     * Fetch all appointments
     */
    public function index()
    {
        $this->authorize('viewAny', Appointment::class);

        $data = $this->appointmentHelper->getAppointments();

        if (empty($data['appointments']) || $data['appointments']->isEmpty()) {
            return $this->errorResponse('No appointments found for the current user.');
        }

        // Transform data if needed (specific to API)
        $data['appointments'] = $data['appointments']->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'date' => $appointment->date,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'status' => $appointment->status,
                'doctor_id' => $appointment->doctor_id,
                'doctor_fname' => $appointment->doctor->user->f_name,
                'doctor_lname' => $appointment->doctor->user->l_name,
                'doctor_speciality' => $appointment->doctor->speciality->name,
                'hourly_rate' => $appointment->doctor->hourly_rate,
                'patient_id' => $appointment->patient_id,
                'patient_fname' => $appointment->patient->user->f_name,
                'patient_lname' => $appointment->patient->user->l_name,
                'patient_email' => $appointment->patient->user->email,
            ];
        });

        return $this->successResponse($data, 'Appointments retrieved successfully');
    }

    /**
     * Create appointment
     */
    public function store(Request $request, AppointmentHelper $appointmentHelper)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'patient_id' => ['nullable', 'exists:patients,id', function ($attribute, $value, $fail) {
                if (Auth::user()->roles === 'admin' && empty($value)) {
                    $fail('The patient ID is required when submitting as admin.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->all());
        }


        $userExist = $appointmentHelper->userExist($request->doctor_id, $request->patient_id, Auth::user()->roles);

        if (!$userExist) {
            if (Auth::user()->roles == 'admin') {
                return $this->errorResponse('Both Doctor and Patient must exist');
            } elseif (Auth::user()->roles == 'patient') {
                return $this->errorResponse('Doctor must exist');
            } else {
                return $this->errorResponse('User does not exist');
            }
        }



        // Validate times
        $timeError = $appointmentHelper->validateTime($request->start_time, $request->end_time);
        if ($timeError) {
            return $this->errorResponse($timeError);
        }

        // Check doctor availability
        $availabilityError = $appointmentHelper->checkDoctorAvailability($request->doctor_id, $request->date, $request->start_time, $request->end_time);

        if ($availabilityError) {
            return $this->errorResponse($availabilityError);
        }

        $appointmentData = [
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ];

        if (Auth::user()->roles === 'patient') {
            $patient = Patient::where('user_id', Auth::id())->first();
            $appointmentData['patient_id'] = $patient->id;
            $appointmentData['status'] = "pending";
            $appointmentData['slot'] = true;
        } else if (Auth::user()->roles === 'admin') {
            $appointmentData['patient_id'] = $request->patient_id;
            $appointmentData['status'] = "booked";
            $appointmentData['slot'] = true;
        } else {
            return $this->errorResponse('Unauthorized', [], 403);
        }

        $appointment = $appointmentHelper->createAppointment($appointmentData);

        return $this->successResponse($appointment, 'Appointment created successfully');
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
    public function update(Request $request, Appointment $appointment, AppointmentHelper $appointmentHelper)
    {
        if ($appointment->status == 'completed' || $appointment->status == 'booked') {
            return $this->errorResponse('This appointment is already completed or booked and cannot be modified.', 422);
        }

        if (!$this->authorize('edit', $appointment)) {
            return $this->errorResponse('You are not authorized to update this appointment.');
        }

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'patient_id' => ['nullable', 'exists:patients,id', function ($attribute, $value, $fail) {
                // Only apply the validation if the logged-in user is an admin
                if (Auth::user()->roles === 'admin' && empty($value)) {
                    return $this->errorResponse('The patient ID is required when submitting as admin.');
                }
            }],
        ]);

        return $this->errorResponse($validator->errors()->all());

        $userExist = $appointmentHelper->userExist($request->doctor_id, $request->patient_id, Auth::user()->roles);

        if (!$userExist) {
            if (Auth::user()->roles == 'admin') {
                return $this->errorResponse('Both Doctor and Patient must exist');
            } elseif (Auth::user()->roles == 'patient') {
                return $this->errorResponse('Doctor must exist');
            } else {
                return $this->errorResponse('User does not exist');
            }
        }

        // Check if validation fails
        if ($validateAppointment->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validateAppointment->errors()->all(),
            ], 422);
        }

        // Validate times

        $timeError = $appointmentHelper->validateTime($request->start_time, $request->end_time);
        if ($timeError) {
            return $this->errorResponse($timeError);
        }

        // Check doctor availability

        $availabilityError = $appointmentHelper->checkDoctorAvailability(
            $request->doctor_id,
            $request->date,
            $request->start_time,
            $request->end_time
        );

        if ($availabilityError) {
            return $this->errorResponse($availabilityError);
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
        Mail::to($patientEmail)
            ->send(new AppointmentMail($appointment, 'patient'));

        Mail::to($doctorEmail)
            ->send(new AppointmentMail($appointment, 'doctor'));

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
            'status' => 'required|in:pending,booked ,cancelled,completed',
        ]);

        $status = $request->status;

        // Check if the appointment is already completed and cannot be modified
        if ($appointment->status === 'completed' && $request->status !== 'completed') {
            return $this->errorResponse('This appointment is already completed and cannot be modified.');
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
            return $this->errorResponse($response);
        }

        // Save the appointment after status change
        $appointment->save();
        return $this->successResponse(null, 'Appointment status updated successfully');
    }
}
