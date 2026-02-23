<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CancelUnpaidAppointments extends Command
{
    protected $signature = 'appointments:cancel-unpaid';
    protected $description = 'Cancelar sesiones que pasaron el plazo de pago (24hs antes)';

    public function handle()
    {
        // Buscar turnos que:
        // - NO están pagados
        // - NO están cancelados
        // - La fecha es AHORA o en menos de 24 horas
        // - El paciente es de tipo 'nuevo' o el turno 'debe_pagarse' es true (depende de la lógica de negocio, 
        //   pero para reservas fijas el usuario pidió que si pasan las 24h sin pagar se cancele)
        
        $unpaidAppointments = Appointment::where('estado_pago', '!=', 'verificado')
            ->where('estado', '!=', 'cancelado')
            ->where('fecha_hora', '>', now())
            ->where('fecha_hora', '<=', now()->addHours(24))
            ->get();

        foreach ($unpaidAppointments as $turno) {
            $turno->update([
                'estado' => 'cancelado',
                'motivo_cancelacion' => 'Cancelada automáticamente: no se pagó en plazo (24hs antes)',
                'cancelado_por' => 'sistema',
            ]);

            $this->info("Turno #{$turno->id} cancelado automáticamente");
        }

        $this->info("Proceso completado: {$unpaidAppointments->count()} turno(s) cancelado(s)");
    }
}
