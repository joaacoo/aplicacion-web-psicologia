<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    
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

    public function payment()
    {
        return $this->hasOne(Payment::class, 'turno_id');
    }
}
