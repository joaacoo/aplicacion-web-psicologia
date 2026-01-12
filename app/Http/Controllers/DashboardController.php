<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function patientDashboard()
    {
        $appointments = Appointment::where('usuario_id', Auth::id())
            ->with('payment')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $availabilities = \App\Models\Availability::all();
        $occupiedSlots = Appointment::where('estado', '!=', 'cancelado')
            ->where('fecha_hora', '>=', now())
            ->pluck('fecha_hora')
            ->toArray();

        $nextAppointment = Appointment::where('usuario_id', Auth::id())
            ->where('estado', 'confirmado')
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->first();

        // Materiales para el paciente o globales
        $resources = \App\Models\Resource::where(function($q) {
            $q->where('paciente_id', Auth::id())
              ->orWhereNull('paciente_id');
        })->latest()->get();

        return view('dashboard.patient', compact('appointments', 'availabilities', 'occupiedSlots', 'nextAppointment', 'resources'));
    }

    public function adminDashboard()
    {
        $appointments = Appointment::with(['user', 'payment'])
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $todayAppointments = Appointment::with(['user', 'payment'])
            ->whereDate('fecha_hora', \Carbon\Carbon::today())
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $patients = \App\Models\User::where('rol', 'paciente')->orderBy('nombre', 'asc')->get();

        // Historial de Acciones y Recursos
        $activityLogs = \App\Models\ActivityLog::with('user')->latest()->take(20)->get();
        $resources = \App\Models\Resource::with('patient')->latest()->get();

        // Registros recientes para mostrar en la agenda
        $recentRegistrations = \App\Models\User::where('rol', 'paciente')
            ->where('created_at', '>=', now()->subMonths(3))
            ->get(['id', 'nombre', 'created_at']);

        return view('dashboard.admin', compact('appointments', 'todayAppointments', 'patients', 'activityLogs', 'resources', 'recentRegistrations'));
    }
}
