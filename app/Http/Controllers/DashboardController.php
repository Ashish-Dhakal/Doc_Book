<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->roles == 'admin') {
            $data['patients'] = Patient::all();
            $data['doctors'] = Doctor::all();
            $data['speciality'] = Speciality::all();
            $data['unavailable_slots'] = AppointmentSlot::where('status', 'booked')
                ->where('status', 'unavailable')
                ->get();

            $date = Carbon::now()->format('Y-m-d');
            $data['appointments'] = Appointment::whereDate('date', $date)->get();
            $data['pending_appointments'] = Appointment::whereIn('status', ['pending', 'unavailable'])->get();
        } elseif (Auth::user()->roles == 'doctor') {
            $userId = Auth::user()->id;
            $doctor = Doctor::where('user_id', $userId)->first();

            // Start the query to get all appointments for the doctor
            $appointments = Appointment::where('doctor_id', $doctor->id);

            // Total patients (unique patients)
            $totalPatients = $appointments->pluck('patient_id')->unique()->count();

            // Appointments by status
            $completedAppointments = $appointments->where('status', 'completed')->count();

            // Appointments today (status booked)
            $todayAppointments = $appointments->where('status', 'booked')
                ->whereDate('date', now()->toDateString())
                ->count();

            // Pass data to the view
            $data = [
                'appointments' => $appointments->get(), // Get the actual appointments
                'totalPatients' => $totalPatients,
                'completedAppointments' => $completedAppointments,
                'todayAppointments' => $todayAppointments
            ];
        } else {
            $userId = Auth::user()->id;
            $data['doctors'] = Doctor::all();
            $patient = Patient::where('user_id', $userId)->first();
            $data['appointments_booked'] = Appointment::where('patient_id', $patient->id)
            ->where('status', 'booked')->get();
            $data['appointments_completed'] = Appointment::where('patient_id', $patient->id)
            ->where('status', 'completed')->get();
            $data['appointments_cancled'] = Appointment::where('patient_id', $patient->id)
            ->where('status', 'cancled')->get();
            $data['pending_appointments'] = Appointment::whereIn('status', ['pending', 'unavailable'])->get();
        }
        return view('dashboard', $data);
    }
}
