<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'turno_id',
        'comprobante_ruta',
        'estado', // pendiente, verificado, rechazado
        'verificado_en',
    ];

    protected $casts = [
        'verificado_en' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'turno_id');
    }
}
