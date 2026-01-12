<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $table = 'disponibilidades';

    protected $fillable = [
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'es_recurrente',
    ];
}
