<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentAutomationCommand extends Command
{
    protected $signature = 'appointments:automation';
    protected $description = 'Handle appointment reminders and auto-cancellations';

    public function handle()
    {
        $this->handleReminders72h();
        $this->handleUltimatum26h();
        $this->handleAutoCancellation24h();
        $this->handleSessionCompletion();
    }

    private function handleReminders72h()
    {
        $target = Carbon::now()->addHours(72);
        $appointments = Appointment::where('estado', 'confirmed')
            ->whereNull('notificado_recordatorio_en')
            ->where('estado_pago', '!=', 'verificado')
            ->whereDate('fecha_hora', $target->toDateString())
            ->get();

        foreach ($appointments as $appt) {
            $appt->user->notify(new \App\Notifications\PatientNotification([
                'title' => '⏰ Recordatorio de Sesión',
                'mensaje' => 'Tu sesión se acerca. Recordá abonar para asegurar tu turno.',
                'link' => route('patient.dashboard'),
                'type' => 'warning'
            ]));
            $appt->update(['notificado_recordatorio_en' => now()]);
        }
    }

    private function handleUltimatum26h()
    {
        $target = Carbon::now()->addHours(26);
        $appointments = Appointment::where('estado', 'confirmed')
            ->whereNull('notificado_ultimatum_en')
            ->where('estado_pago', '!=', 'verificado')
            ->where('fecha_hora', '<=', $target)
            ->where('fecha_hora', '>', Carbon::now()->addHours(24))
            ->get();

        foreach ($appointments as $appt) {
            $appt->user->notify(new \App\Notifications\PatientNotification([
                'title' => '⚠️ Última oportunidad',
                'mensaje' => 'Tenés 2 horas para abonar o tu turno se cancelará automáticamente sin devolución (política de 24hs).',
                'link' => route('patient.dashboard'),
                'type' => 'error'
            ]));
            $appt->update(['notificado_ultimatum_en' => now()]);
        }
    }

    private function handleAutoCancellation24h()
    {
        $appointments = Appointment::where('estado', 'confirmed')
            ->where('estado_pago', '!=', 'verificado')
            ->where('fecha_hora', '<=', Carbon::now()->addHours(24))
            ->where('fecha_hora', '>', Carbon::now())
            ->get();

        foreach ($appointments as $appt) {
            $appt->update([
                'estado' => 'cancelado',
                'motivo_cancelacion' => 'Cancelación automática por falta de pago (24hs antes).'
            ]);
            
            $appt->user->notify(new \App\Notifications\PatientNotification([
                'title' => '❌ Turno Cancelado',
                'mensaje' => 'Tu turno fue cancelado automáticamente por falta de verificación de pago 24hs antes.',
                'link' => route('patient.dashboard'),
                'type' => 'error'
            ]));

            // Notify Admin
            $admin = \App\Models\User::where('rol', 'admin')->first();
            if ($admin) {
                $admin->notify(new \App\Notifications\AdminNotification([
                    'title' => '📉 Turno Liberado (Auto)',
                    'mensaje' => 'El turno de ' . $appt->user->nombre . ' se canceló automáticamente por falta de pago.',
                    'link' => route('admin.agenda'),
                    'type' => 'alert'
                ]));
            }
        }
    }

    private function handleSessionCompletion()
    {
        // Turno + 45 min = Finalizado
        $appointments = Appointment::where('estado', 'confirmado')
            ->where('fecha_hora', '<=', Carbon::now()->subMinutes(45))
            ->get();

        foreach ($appointments as $appt) {
            $appt->update(['estado' => Appointment::ESTADO_FINALIZADO]);
        }
    }
}
