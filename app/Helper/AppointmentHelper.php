<?php

namespace App\Helper;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Speciality;
use App\Models\Appointment;
use App\Models\PatientHistory;
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

    // Function to check if both doctor and patient exist
    public function userExist($doctor_id, $patient_id, $role)
    {
        // If user is an admin, both doctor and patient need to exist
        if ($role == 'admin') {
            $doctor = Doctor::where('id', $doctor_id)->first();
            $patient = Patient::where('id', $patient_id)->first();

            if ($doctor && $patient) {
                return true;
            }
        }

        // If user is a patient, only doctor needs to exist
        if ($role == 'patient') {
            $doctor = Doctor::where('id', $doctor_id)->first();
            return $doctor;
            if ($doctor) {
                return true;
            }
        }

        return false;
    }



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


        $appointmentSlot = new AppointmentSlot();
        $appointmentSlot->doctor_id = $data['doctor_id'];
        $appointmentSlot->date = $data['date'];
        $appointmentSlot->start_time = $data['start_time'];
        $appointmentSlot->end_time = $data['end_time'];
        $appointmentSlot->status = 'booked';
        $appointmentSlot->save();

        return $appointment;
    }

    public function handlePending(Appointment $appointment, $status, $appointmentSlot)
    {
        $appointment->status = 'pending';

        $appointmentInfo = AppointmentSlot::where('doctor_id', $appointment->doctor_id)
            ->where('date', $appointment->date)
            ->where('start_time', $appointment->start_time)
            ->where('end_time', $appointment->end_time)
            ->where('status', 'booked')
            ->first();

        if ($appointmentInfo) {
            return 'This time slot is already booked';
        }

        return null;
    }

    public function handleBooked(Appointment $appointment)
    // public function handleBooked(Appointment $appointment, AppointmentSlot $appointmentSlot, $status)
    {
        $appointment->status = 'booked';
        $appointment->save();
        // $appointmentSlot->status = 'booked';
        // $appointmentSlot->start_time = $appointment->start_time;
        // $appointmentSlot->doctor_id = $appointment->doctor_id;
        // $appointmentSlot->end_time = $appointment->end_time;
        // $appointmentSlot->date = $appointment->date;

        // $doctorInfo = AppointmentSlot::where('doctor_id', $appointment->doctor_id)
        //     ->where('date', $appointment->date)
        //     ->where('start_time', $appointment->start_time)
        //     ->where('end_time', $appointment->end_time)
        //     ->where('status', 'booked')
        //     ->first();

        // if ($doctorInfo) {
        //     return 'This time slot is already booked';
        // } else {
        //     $appointmentSlot->save();
        // }

        return null;
    }

    public function handleCancelled(Appointment $appointment, AppointmentSlot $appointmentSlot, $status)
    {
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
        return 'Appointment cancelled successfully';
    }

    public function handleCompleted(Appointment $appointment, $status)
    {
        if ($appointment->status !== 'completed') {
            $appointment->status = 'completed';

            AppointmentSlot::where('doctor_id', $appointment->doctor_id)
                ->where('date', $appointment->date)
                ->where('start_time', $appointment->start_time)
                ->where('end_time', $appointment->end_time)
                ->delete();
        }

        if (!$appointment->reviews) {
            return 'Please add review first';
        }

        // Calculate the duration and payment
        $startTime = Carbon::parse($appointment->start_time);
        $endTime = Carbon::parse($appointment->end_time);
        $durationInHours = $startTime->diffInHours($endTime);
        $totalFee = $durationInHours * $appointment->doctor->hourly_rate;

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'amount' => $totalFee,
            'patient_id' => $appointment->patient_id,
        ]);

        $firstReview = $appointment->reviews->first();
        if (!$firstReview) {
            return 'Doctor is yet to add review first';
        }

        PatientHistory::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'review_id' => $firstReview->id,
            'payment_id' => $payment->id,
        ]);

        $appointment->save();
        // return 'Appointment marked as completed';
    }
}
