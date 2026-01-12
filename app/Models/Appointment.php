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
    ];

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
