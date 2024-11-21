<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AppointmentPolicy
{
    /**
     * Determine if the user can view any appointments.
     */

    public function userId()
    {
        if (Auth::user()->roles == 'doctor') {
            $userId =  Auth::user()->id;
            $doctorId = Doctor::where('user_id', $userId)->first();
            dd($doctorId->id);
            return $patientId->id;
        } elseif (Auth::user()->roles == 'patient') {
            $userId =  Auth::user()->id;
            $patientId = Patient::where('user_id', $userId)->first();
            return $patientId->id;
        }
    }

    public function viewAny(User $user)
    {
        return in_array($user->roles, ['admin', 'doctor', 'patient']);
    }

    /**
     * Determine if the user can view a specific appointment.
     */
    public function view(User $user, Appointment $appointment)
    {

        return $user->roles === 'admin' ||
            ($user->roles === 'patient' && $appointment->patient_id === $this->userId());
    }

    /**
     * Determine if the user can create an appointment.
     */
    public function create(User $user)
    {
        return $user->roles === 'admin' || $user->roles === 'patient';
    }

    public function edit(User $user, Appointment $appointment)
    {
        // Admins can edit any appointment
        if ($user->roles === 'admin') {
            return true;
        }

        // Patients can edit only their own appointments
        if ($user->roles === 'patient' && $appointment->patient_id === $user->id) {
            return true;
        }

        // Default: not authorized
        return false;
    }



    /**
     * Determine if the user can update an appointment.
     */
    public function update(User $user, Appointment $appointment)
    {
        return $user->roles === 'admin' ||
            ($user->roles === 'patient' && $appointment->patient_id === $this->userId());
    }

    /**
     * Determine if the user can delete an appointment.
     */
    public function delete(User $user, Appointment $appointment)
    {
        return $user->roles === 'admin';
    }
}
