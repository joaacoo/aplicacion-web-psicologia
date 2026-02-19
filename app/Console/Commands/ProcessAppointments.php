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
        
        // 1. Reminders (T-24h and T-1h)
        $appointmentsForReminder = \App\Models\Appointment::whereIn('estado', ['pendiente', 'confirmado'])
            ->where('fecha_hora', '>=', $now)
            ->where('fecha_hora', '<=', $now->copy()->addHours(48))
            ->get();

        foreach ($appointmentsForReminder as $appt) {
            $appointmentTime = $appt->fecha_hora;
            $user = $appt->user;
            
            // T-24h Reminder
            if (!$appt->notificado_recordatorio_en &&
                $now->greaterThanOrEqualTo($appointmentTime->copy()->subHours(24)) && 
                $now->lessThan($appointmentTime->copy()->subHours(20))) {
                
                try {
                    $tipoAviso = ($appt->estado === 'confirmado') ? 'recordatorio_confirmado' : 'recordatorio';
                    $msg = ($appt->estado === 'confirmado') 
                        ? 'Recordá que tenés una sesión mañana a las ' . $appointmentTime->format('H:i') . ' hs.'
                        : 'Recordá que tenés una sesión mañana a las ' . $appointmentTime->format('H:i') . ' hs. Recordá subir el comprobante para confirmar.';

                    // Mail
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AppointmentReminder($user->nombre, $appointmentTime->format('d/m H:i'), $tipoAviso));
                    
                    // Web Notification
                    $user->notify(new \App\Notifications\PatientNotification([
                        'title' => 'Recordatorio de Sesión',
                        'mensaje' => $msg,
                        'link' => route('patient.dashboard'),
                        'type' => 'info'
                    ]));

                    $appt->update(['notificado_recordatorio_en' => $now]);
                    $this->info("Sent 24h reminder to: " . $user->email);
                } catch (\Exception $e) {
                    $this->error("Failed to send 24h reminder to " . $user->email . ": " . $e->getMessage());
                }
            }

            // T-1h Reminder
            if (!$appt->notificado_una_hora_en &&
                $now->greaterThanOrEqualTo($appointmentTime->copy()->subMinutes(60)) && 
                $now->lessThan($appointmentTime)) {
                
                try {
                    // Mail
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AppointmentReminder($user->nombre, $appointmentTime->format('H:i'), 'proxima_sesion'));
                    
                    // Web Notification
                    $user->notify(new \App\Notifications\PatientNotification([
                        'title' => 'Sesión en 1 hora',
                        'mensaje' => 'Tu sesión comienza en 1 hora (a las ' . $appointmentTime->format('H:i') . ' hs).',
                        'link' => route('patient.dashboard'),
                        'type' => 'warning'
                    ]));

                    $appt->update(['notificado_una_hora_en' => $now]);
                    $this->info("Sent 1h reminder to: " . $user->email);
                } catch (\Exception $e) {
                    $this->error("Failed to send 1h reminder to " . $user->email . ": " . $e->getMessage());
                }
            }
        }

        // 2. Ultimatum & Cancellation (Specific to 'pendiente' & 'nuevo' usually, but let's see original logic)
        // Original logic was specific to pending payments. We should keep that for "Ultimatum".
         $pendingAppointments = \App\Models\Appointment::where('estado', 'pendiente')
            ->whereDoesntHave('payment')
            ->whereHas('user', function($q) { $q->where('tipo_paciente', 'nuevo'); })
            ->get();

        foreach ($pendingAppointments as $appt) {
             $appointmentTime = $appt->fecha_hora;
             
             // ... Ultimatum logic remains only for pending new patients ...

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
                
                // Notify via DB and Mail
                $appt->user->notify(new \App\Notifications\PatientNotification([
                    'title' => 'Turno Cancelado Automat.',
                    'mensaje' => 'Tu turno para el ' . $appt->fecha_hora->format('d/m H:i') . ' ha sido cancelado automáticamente por falta de pago.',
                    'link' => route('patient.dashboard'),
                    'type' => 'cancelación_automática'
                ]));

                $this->info("Cancelled appointment: " . $appt->id);
            }
        }

        // 3. Admin Reminder: Pending Payment Proofs for TOMORROW
        // "sino acpeto el omporbante y la sesion es al otro dia"
        
        $tomorrowStart = $now->copy()->addDay()->startOfDay();
        $tomorrowEnd = $now->copy()->addDay()->endOfDay();

        $appointmentsTomorrowWithPendingProof = \App\Models\Appointment::whereBetween('fecha_hora', [$tomorrowStart, $tomorrowEnd])
            ->whereHas('payment', function($q) {
                $q->where('estado', 'pendiente');
            })
            ->get();

        if ($appointmentsTomorrowWithPendingProof->count() > 0) {
            $admin = \App\Models\User::where('rol', 'admin')->first();
            if ($admin) {
                // Check if we already notified admin about pending proofs for tomorrow to avoid spam
                // We can use a cache key or just send it (command runs hourly?)
                // If command runs hourly, we should only send once a day or use a flag.
                // For now, let's filter by time: only send if it's 8 AM or if we haven't sent it today.
                // Simpler: Just create a notification in DB.
                
                foreach ($appointmentsTomorrowWithPendingProof as $appt) {
                     // Check if notification already exists using standard notifications table
                     $exists = $admin->notifications()
                        ->where('data->mensaje', 'like', '%Revisar comprobante pendiente%')
                        ->where('created_at', '>=', $now->copy()->startOfDay())
                        ->exists();

                     if (!$exists) {
                        $admin->notify(new \App\Notifications\AdminNotification([
                            'title' => 'Comprobante Pendiente',
                            'mensaje' => '⚠️ Revisar comprobante pendiente para el turno de mañana: ' . $appt->user->nombre,
                            'link' => route('payments.showProof', $appt->payment->id),
                            'type' => 'pago_pendiente'
                        ]));
                     }
                }
            }
        }

        return 0;
    }
}
