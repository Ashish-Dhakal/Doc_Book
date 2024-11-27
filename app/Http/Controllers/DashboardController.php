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
use Illuminate\Support\Facades\DB;
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


            // data to show in chart
            $data['gender_distribution'] = User::select('gender', DB::raw('count(*) as count'))
                ->where('roles', 'patient')
                ->groupBy('gender')
                ->get();

                $speciality = Doctor::select('specialities.name as specialty', DB::raw('count(*) as count'))
                ->join('specialities', 'doctors.speciality_id', '=', 'specialities.id')
                ->groupBy('specialities.name')
                ->get();
        
            // Prepare data for the chart
            $data['chartData'] = [
                'labels' => $speciality->pluck('specialty'),
                'data' => $speciality->pluck('count')
            ];

            $appointments = Appointment::select(
                DB::raw("COUNT(*) as count"),
                DB::raw("MONTHNAME(date) as month_name"),
                DB::raw("MONTH(date) as month_number")
            )
                ->whereYear('date', date('Y'))
                ->groupBy(DB::raw("MONTH(date), MONTHNAME(date)"))
                ->orderBy('month_number') // Ensures months are ordered chronologically
                ->pluck('count', 'month_name');

            $data['labels1'] = $appointments->keys(); // Array of month names
            $data['data1'] = $appointments->values(); // Array of counts












            // dd($data['gender_distribution']);
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
