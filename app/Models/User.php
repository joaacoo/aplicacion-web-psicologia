<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios'; // Tabla en español

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    protected $fillable = [
        'nombre',
        'email',
        // 'telefono', // Moved to Paciente
        'password',
        'rol', // admin, paciente
        // 'tipo_paciente', // Moved to Paciente
        // 'duracion_sesion', // Moved to Profesional
        // 'intervalo_sesion', // Moved to Profesional
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    protected static function boot()
    {
        parent::boot();
    }

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

    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'user_id');
    }

    public function profesional()
    {
        return $this->hasOne(Profesional::class, 'user_id');
    }
    
    // Alias para compatibilidad con Auth de Laravel que busca 'name' a veces
    public function getNameAttribute() {
        return $this->nombre;
    }

    // Accessors para compatibilidad con refactor de Base de Datos (Profesional)
    public function getDuracionSesionAttribute($value)
    {
        return $this->profesional->duracion_sesion ?? $value ?? 45;
    }

    public function getIntervaloSesionAttribute($value)
    {
        return $this->profesional->intervalo_sesion ?? $value ?? 15;
    }

    public function getGoogleCalendarUrlAttribute($value)
    {
        return $this->profesional->google_calendar_url ?? $value;
    }

    public function getIcalTokenAttribute($value)
    {
        return $this->profesional->ical_token ?? $value;
    }

    // Accessors para compatibilidad con refactor de Base de Datos (Paciente)
    public function getTipoPacienteAttribute($value)
    {
        return $this->paciente->tipo_paciente ?? $value ?? 'nuevo';
    }

    public function getHonorarioPactadoAttribute($value)
    {
        return $this->paciente->honorario_pactado ?? 0;
    }

    public function getTelefonoAttribute($value)
    {
         return $this->paciente->telefono ?? $value; // Fallback to local if migration in progress
    }
}
