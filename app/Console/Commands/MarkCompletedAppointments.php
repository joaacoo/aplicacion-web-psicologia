<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;

class MarkCompletedAppointments extends Command
{
    protected $signature = 'appointments:mark-completed';
    protected $description = 'Marcar sesiones como realizadas si ya pasó la hora';

    public function handle()
    {
        // Buscar turnos que:
        // - NO están marcados como realizados
        // - NO están cancelados
        // - Ya pasó la hora
        $completedAppointments = Appointment::where('estado', '!=', 'completado')
            ->where('estado', '!=', 'cancelado')
            ->where('fecha_hora', '<', now())
            ->where('estado_realizado', '!=', 'realizado')
            ->get();

        foreach ($completedAppointments as $turno) {
            $turno->markAsRealizado();
            
            $this->info("Turno #{$turno->id} marcado como realizado");
        }

        $this->info("Proceso completado: {$completedAppointments->count()} turno(s) marcado(s)");
    }
}
