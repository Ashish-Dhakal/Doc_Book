<?php

namespace App\Helper;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Auth;

class AppointmentHelper
{
    /**
     * Create a new class instance.
     */
    public function getAppointments()
    {
        $user = Auth::user();
        $userId = $user->id;

        $appointments = [];
        $specialities = Speciality::all();

        if ($user->roles === 'admin') {
            $appointments = Appointment::paginate(5);
        } elseif ($user->roles === 'patient') {
            $patient = Patient::where('user_id', $userId)->first();
            if ($patient) {
                $appointments = Appointment::where('patient_id', $patient->id)->paginate(5);
            }
        } elseif ($user->roles === 'doctor') {
            $doctor = Doctor::where('user_id', $userId)->first();
            if ($doctor) {
                $appointments = Appointment::where('doctor_id', $doctor->id)
                    ->where('status', 'booked')->get();
            }
        }

        return compact('appointments', 'specialities');
    }

    /**
     * Validate the start and end times of an appointment.
     */
    public function validateTime($startTime, $endTime)
    {
        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);

        if ($endTimeCarbon->lt($startTimeCarbon)) {
            return 'End time should be after start time.';
        }

        return null;
    }

    /**
     * Check if the doctor is available for the given time slot.
     *
     * This method checks for conflicts with existing appointments and
     * the doctor's availability in the AppointmentSlot table.
     *
     * @param int $doctorId The doctor ID.
     * @param string $date The date of the appointment.
     * @param string $startTime The start time of the appointment.
     * @param string $endTime The end time of the appointment.
     *
     * @return string|null The error message if the doctor is not available.
     */

    public function checkDoctorAvailability($doctorId, $date, $startTime, $endTime)
    {
        // Check appointment conflicts
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
            return 'The selected time slot is already booked for this doctor.';
        }

        // Check availability in AppointmentSlot
        $appointmentSlot = AppointmentSlot::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereIn('status', ['unavailable', 'booked'])
                    ->where(function ($subQuery) use ($startTime, $endTime) {
                        $subQuery->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    });
            })
            ->exists();

        if ($appointmentSlot) {
            return 'Doctor is not available at that time slot.';
        }

        return null;
    }

    /**
     * Create a new appointment and update appointment slots if needed.
     *
     * @param array $data The data required to create the appointment.
     *                    It should include doctor_id, date, start_time, end_time, 
     *                    and optional patient_id, status, and slot.
     * @return Appointment The created appointment instance.
     */
    public function createAppointment($data)
    {
        $appointment = new Appointment();
        $appointment->patient_id = $data['patient_id'] ?? null;
        $appointment->doctor_id = $data['doctor_id'];
        $appointment->date = $data['date'];
        $appointment->start_time = $data['start_time'];
        $appointment->end_time = $data['end_time'];
        $appointment->status = $data['status'] ?? 'pending';
        $appointment->save();

        // Update appointment slots if needed
        if (!empty($data['slot'])) {
            $appointmentSlot = new AppointmentSlot();
            $appointmentSlot->doctor_id = $data['doctor_id'];
            $appointmentSlot->date = $data['date'];
            $appointmentSlot->start_time = $data['start_time'];
            $appointmentSlot->end_time = $data['end_time'];
            $appointmentSlot->status = 'booked';
            $appointmentSlot->save();
        }

        return $appointment;
    }
}
