<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any appointments.
     */
    public function viewAny(User $user): bool
    {
        // Admin can see all appointments, patients only their own
        return true; // Controller will filter based on role
    }

    /**
     * Determine whether the user can view the appointment.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Admin can view any appointment
        if ($user->rol === 'admin') {
            return true;
        }
        
        // Patient can only view their own appointments
        return $user->id === $appointment->usuario_id;
    }

    /**
     * Determine whether the user can create appointments.
     */
    public function create(User $user): bool
    {
        // Only patients can create appointments (admins manage them differently)
        return $user->rol === 'paciente';
    }

    /**
     * Determine whether the user can update the appointment.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Admin can update any appointment (confirm, cancel, etc.)
        if ($user->rol === 'admin') {
            return true;
        }
        
        // Patient can only update their own appointments (e.g., cancel)
        return $user->id === $appointment->usuario_id;
    }

    /**
     * Determine whether the user can delete the appointment.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        // Same as update - admin can delete any, patient only their own
        if ($user->rol === 'admin') {
            return true;
        }
        
        return $user->id === $appointment->usuario_id;
    }

    /**
     * Determine whether the user can confirm appointments (admin only).
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        // Only admin can confirm appointments
        return $user->rol === 'admin';
    }

    /**
     * Determine whether the user can cancel appointments.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        // Admin can cancel any appointment
        if ($user->rol === 'admin') {
            return true;
        }
        
        // Patient can only cancel their own appointments
        return $user->id === $appointment->usuario_id;
    }
}
