<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'turnos';

    protected $fillable = [
        'usuario_id',
        'fecha_hora',
        'estado', // pendiente, confirmado, cancelado, completado
        'es_recurrente',
        'notas',
        'modalidad',
        'link_reunion',
        'vence_en',
        'notificado_recordatorio_en',
        'notificado_ultimatum_en',
        'debe_pagarse',
        'paciente_id',
        'monto_final',
    ];

    /**
     * Determina si el turno genera deuda.
     * Es deuda si está confirmado (y no pagado) O si está cancelado pero se cobra igual.
     */
    public function generaDeuda(): bool
    {
        return $this->estado === 'confirmado' || ($this->estado === 'cancelado' && $this->debe_pagarse);
    }

    protected $casts = [
        'fecha_hora' => 'datetime',
        'es_recurrente' => 'boolean',
        'vence_en' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    protected static function booted()
    {
        static::creating(function ($turno) {
            // Ensure patient relationship is loaded or accessible
            // If created via $paciente->turnos()->create(), the relation might be set.
            // But usually we just have usuario_id or paciente_id.
            
            // If paciente_id is set, use it. If not, try to deduce from usuario_id
            $paciente = null;
            if ($turno->paciente_id) {
                $paciente = Paciente::find($turno->paciente_id);
            } elseif ($turno->usuario_id) {
                $paciente = Paciente::where('user_id', $turno->usuario_id)->first();
                if ($paciente) {
                    $turno->paciente_id = $paciente->id;
                }
            }

            if ($paciente) {
                $turno->monto_final = $turno->paciente_id ? $paciente->honorario_pactado : 0;
                
                // Fallback to global setting if 0 or null
                if (!$turno->monto_final) {
                    $turno->monto_final = \App\Models\Setting::get('precio_base_sesion', 25000);
                }

                if ($paciente->tipo_paciente === 'nuevo') {
                    $turno->debe_pagarse = true;
                    // Vence 1 día antes a la misma hora
                    if ($turno->fecha_hora) {
                         $turno->vence_en = $turno->fecha_hora->copy()->subDay();
                    }
                } else {
                    $turno->debe_pagarse = false;
                }
            }
        });
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'turno_id');
    }
}
