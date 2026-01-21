<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    use HasFactory;

    protected $table = 'profesionales';

    protected $fillable = [
        'user_id',
        'google_calendar_url',
        'ical_token',
        'duracion_sesion',
        'intervalo_sesion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
