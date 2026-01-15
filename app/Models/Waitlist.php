<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'name',
        'phone',
        'availability',
        'modality',
        'fecha_especifica',
        'dia_semana',
        'hora_inicio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
