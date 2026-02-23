<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Enviar recordatorios de turnos: 72hs, 26hs y 24hs antes';

    public function handle()
    {
        $ahora = now();

        $appointments = Appointment::where('estado', '!=', 'cancelado')
            ->where('estado', '!=', \App\Models\Appointment::ESTADO_FINALIZADO)
            ->where('fecha_hora', '>', $ahora)
            ->with(['user', 'payment'])
            ->get();

        $notified72hs = 0;
        $notified26hs = 0;

        foreach ($appointments as $appointment) {
            $hoursUntil = $ahora->diffInHours($appointment->fecha_hora, false);
            $isPaid = $appointment->payment && $appointment->payment->estado === 'verificado';

            if ($hoursUntil > 0) {
                if ($hoursUntil >= 72 && $hoursUntil <= 73 && !$isPaid) {
                    $this->sendReminder($appointment, 72);
                    $notified72hs++;
                }
                elseif ($hoursUntil >= 26 && $hoursUntil <= 27 && !$isPaid) {
                    $this->sendReminder($appointment, 26);
                    $notified26hs++;
                }
            }
        }

        $this->info("Recordatorios enviados: {$notified72hs} (72hs) y {$notified26hs} (26hs)");
    }

    private function sendReminder($appointment, $hours)
    {
        if (!$appointment->user) return;

        $message = match($hours) {
            72 => 'Recordatorio: Tu sesión se acerca, recordá abonar.',
            26 => 'Última oportunidad para pagar o cancelar sin perder el turno.',
            default => 'Recordatorio de tu sesión.'
        };

        $appointment->user->notify(new \App\Notifications\PatientNotification([
            'title' => "Tu sesión es en {$hours} horas",
            'mensaje' => $message . ' Tu turno está programado para el ' . $appointment->fecha_hora->format('d/m H:i'),
            'link' => route('patient.dashboard'),
            'type' => 'recordatorio'
        ]));
    }
}
