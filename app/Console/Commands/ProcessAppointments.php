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
        
        // 1. Reminders (T-24h) - NOW FOR ALL PATIENTS (Confirmed + Pendiente)
        $appointmentsForReminder = \App\Models\Appointment::whereIn('estado', ['pendiente', 'confirmado'])
            ->get(); // We filter mostly in loop or improve query for time

        // Better Query for T-24h
        // We need appointments that happen tomorrow around this time.
        // Actually the loop logic below checks times specific to each appointment.
        // Let's get all active appointments that haven't been notified yet.
        $appointmentsForReminder = \App\Models\Appointment::whereIn('estado', ['pendiente', 'confirmado'])
            ->whereNull('notificado_recordatorio_en')
            ->where('fecha_hora', '>=', $now) // Future appointments
            ->where('fecha_hora', '<=', $now->copy()->addHours(48)) // Optimization: don't load everything
            ->get();

        foreach ($appointmentsForReminder as $appt) {
            $appointmentTime = $appt->fecha_hora;
            
            // T-24h Reminder (Logic: Between T-25h and T-21h to be safe and catch the window)
            // Original logic: $now >= Appt-24h AND $now < Appt-21h
            if ($now->greaterThanOrEqualTo($appointmentTime->copy()->subHours(24)) && 
                $now->lessThan($appointmentTime->copy()->subHours(21))) {
                
                try {
                    \Illuminate\Support\Facades\Mail::to($appt->user->email)->send(new \App\Mail\AppointmentReminder($appt->user->nombre, $appt->fecha_hora->format('d/m H:i'), 'recordatorio'));
                    $appt->update(['notificado_recordatorio_en' => $now]);
                    $this->info("Sent reminder to: " . $appt->user->email);
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder to " . $appt->user->email);
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
                
                // Notify via DB
                \App\Models\Notification::create([
                    'usuario_id' => $appt->usuario_id,
                    'mensaje' => 'Tu turno para el ' . $appt->fecha_hora->format('d/m H:i') . ' ha sido cancelado automáticamente por falta de pago.',
                    'link' => route('patient.dashboard')
                ]);

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
                     // Check if notification already exists for this specific issue today
                     $exists = \App\Models\Notification::where('usuario_id', $admin->id)
                        ->where('mensaje', 'like', '%Revisar comprobante pendiente%')
                        ->where('created_at', '>=', $now->copy()->startOfDay())
                        ->exists();

                     if (!$exists) {
                        \App\Models\Notification::create([
                            'usuario_id' => $admin->id,
                            'mensaje' => '⚠️ Revisar comprobante pendiente para el turno de mañana: ' . $appt->user->nombre,
                            'link' => route('payments.showProof', $appt->payment->id)
                        ]);
                        
                        // Optional: Email to admin? User said "ponga", sounds like notification/alert.
                     }
                }
            }
        }

        return 0;
    }
}
