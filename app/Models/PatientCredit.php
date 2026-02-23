<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientCredit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'paciente_id',
        'appointment_id',
        'amount',
        'reason',
        'status',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
