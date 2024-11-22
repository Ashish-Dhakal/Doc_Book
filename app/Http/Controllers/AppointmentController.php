<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class AppointmentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
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
            $data['appointments'] = Appointment::where('patient_id', $patient->id)->get();
        } elseif (Auth::user()->roles == 'admin') {
            $data['appointments'] = Appointment::all();
        } elseif (Auth::user()->roles == 'doctor') {
            $data['appointments'] = Appointment::where('doctor_id', $doctorId->id)
                ->where('status', 'booked')
                ->get();
        } else {
            return redirect()->route('login');
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
    public function store(Request $request, AppointmentSlot $appointmentSlot)
    {
        // dd($request->toArray());

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

        // Get the input times (24-hour format)
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Convert the times to Carbon instances (don't format to 12-hour yet)
        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);


        if ($endTimeCarbon->lt($startTimeCarbon)) {
            return redirect()->back()->withErrors(['end_time' => 'End time should be after start time.']);
        }

        // check if the doctor is availabelin that time or not
        $doctorId = $request->input('doctor_id'); // get the doctor id
        $date = $request->input('date'); // get the date
        // $appointmentSlots = AppointmentSlot::where('doctor_id', $doctorId)
        //     ->where('date', $date)
        //     ->where('start_time', '<=', $startTime)
        //     ->where('end_time', '>=', $endTime)
        //     ->where(function ($query) {
        //         $query->where('status', 'unavailable')
        //             ->orWhere('status', 'booked');
        //     })
        //     ->first();


        $appointmentSlots = AppointmentSlot::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($subQuery) use ($startTime, $endTime) {
                    $subQuery->where('status', ['unavailable', 'booked'])
                        ->where(function ($timeQuery) use ($startTime, $endTime) {

                            $timeQuery->where('start_time', '<', $endTime)
                                ->where('end_time', '>', $startTime);
                        });
                });
            })
            ->first();
        if ($appointmentSlots) {
            return redirect()->back()->withErrors(['start_time' => 'Doctor is not available in that time.']);
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
            $appointment->save();
            return redirect()->route('appointments.index')->with('success', 'Appointment created successfully');
        } elseif (Auth::user()->roles == 'admin') {
            $appointment = new Appointment();
            $appointment->patient_id = $request->input('patient_id');
            $appointment->doctor_id = $request->input('doctor_id');
            $appointment->date = $request->input('date');
            $appointment->start_time = $request->input('start_time');
            $appointment->end_time = $request->input('end_time');
            $appointment->status = 'booked';
            $appointment->save();

            // dd($request->input('doctor_id'));
            $appointmentSlot = new AppointmentSlot();
            $appointmentSlot->doctor_id = $request->input('doctor_id');
            $appointmentSlot->date = $request->input('date');
            $appointmentSlot->start_time = $request->input('start_time');
            $appointmentSlot->end_time = $request->input('end_time');
            $appointmentSlot->status = 'booked';
            $appointmentSlot->save();

            return redirect()->route('appointments.index')->with('success', 'Appointment created successfully');
        } else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $appointment = Appointment::find($id);

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
            'time' => 'required',
        ]);

        $appointment->doctor_id = $request->input('doctor_id');
        $appointment->date = $request->input('date');
        $appointment->time = $request->input('time');
        $appointment->save();
        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully');
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
            'status' => 'required|in:pending,booked,rescheduled,cancelled,completed',
        ]);
        $status = $request->status;

        if ($appointment->status === 'completed' && $request->status !== 'completed') {
            return redirect()->route('appointments.index')->with('error', 'This appointment is already completed and cannot be modified.');
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
                    return redirect()->route('appointments.index')->with('error', 'This time slot is already booked');
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
                    return redirect()->route('appointments.index')->with('error', 'This time slot is already booked');
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

                return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully');
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
                // After completing, redirect with a flag to show the review modal
                $appointment->save();
                return redirect()->route('appointments.index')->with('success', 'Appointment marked as completed')->with('showReviewModal', true);
                break;
            default:
                $appointment->status = 'pending';
        }
        $appointment->save();
        return redirect()->route('appointments.index')->with('success', 'Appointment status updated successfully');
    }
}
