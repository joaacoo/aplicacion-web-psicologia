<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'turno_id',
        'comprobante_ruta',
        'estado', // pendiente, verificado, rechazado
        'verificado_en',
    ];

    protected $casts = [
        'verificado_en' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'turno_id');
    }
}
