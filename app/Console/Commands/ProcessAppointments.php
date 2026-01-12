<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // 1. Reminders (T-24h)
        // Appointments where now is >= (vence_en + 4h) AND not notified yet
        // Since vence_en is T-20h, T-24h is vence_en + 4 hours (looking back from appointment)
        // Wait, T-24h is EARLIER than T-20h.
        // If Appt is at 15:00. T-24 is 15:00 prev. T-20 is 19:00 prev.
        // So Reminder (T-24) happens BEFORE vence_en.
        // Rule: T-24 is Appt - 24 hours.
        
        $pendingAppointments = \App\Models\Appointment::where('estado', 'pendiente')
            ->whereDoesntHave('payment')
            ->whereHas('user', function($q) { $q->where('tipo_paciente', 'nuevo'); })
            ->get();

        foreach ($pendingAppointments as $appt) {
            $appointmentTime = $appt->fecha_hora;
            
            // T-24h Reminder
            if ($now->greaterThanOrEqualTo($appointmentTime->copy()->subHours(24)) && 
                $now->lessThan($appointmentTime->copy()->subHours(21)) &&
                !$appt->notificado_recordatorio_en) {
                
                \Illuminate\Support\Facades\Mail::to($appt->user->email)->send(new \App\Mail\AppointmentReminder($appt->user->nombre, $appt->fecha_hora->format('d/m H:i'), 'recordatorio'));
                $appt->update(['notificado_recordatorio_en' => $now]);
                $this->info("Sent reminder to: " . $appt->user->email);
            }

            // T-21h Ultimatum
            if ($now->greaterThanOrEqualTo($appointmentTime->copy()->subHours(21)) && 
                $now->lessThan($appointmentTime->copy()->subHours(20)) &&
                !$appt->notificado_ultimatum_en) {
                
                \Illuminate\Support\Facades\Mail::to($appt->user->email)->send(new \App\Mail\AppointmentReminder($appt->user->nombre, $appt->fecha_hora->format('d/m H:i'), 'ultimatum'));
                $appt->update(['notificado_ultimatum_en' => $now]);
                $this->info("Sent ultimatum to: " . $appt->user->email);
            }

            // T-20h Cancellation (vence_en)
            if ($now->greaterThanOrEqualTo($appt->vence_en)) {
                $appt->update(['estado' => 'cancelado']);
                
                // Notify via DB
                \App\Models\Notification::create([
                    'usuario_id' => $appt->usuario_id,
                    'mensaje' => 'Tu turno para el ' . $appt->fecha_hora->format('d/m H:i') . ' ha sido cancelado automáticamente por falta de pago.',
                    'link' => route('patient.dashboard')
                ]);

                $this->info("Cancelled appointment: " . $appt->id);
            }
        }

        return 0;
    }
}
