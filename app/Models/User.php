<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios'; // Tabla en español

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'password',
        'rol', // admin, paciente
        'tipo_paciente', // nuevo, frecuente
        'duracion_sesion',
        'intervalo_sesion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación con Turnos
    public function turnos()
    {
        return $this->hasMany(Appointment::class, 'usuario_id');
    }

    // Relación con Documentos
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    
    // Alias para compatibilidad con Auth de Laravel que busca 'name' a veces
    public function getNameAttribute() {
        return $this->nombre;
    }
}
