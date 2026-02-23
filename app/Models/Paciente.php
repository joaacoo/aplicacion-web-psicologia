<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pacientes';

    protected $fillable = [
        'user_id',
        'tipo_paciente',
        'honorario_pactado',
        'precio_personalizado', // New column
        'telefono',
        'meet_link',
    ];

    protected $casts = [
        'telefono' => 'encrypted',
        'meet_link' => 'encrypted',
        'precio_personalizado' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paciente) {
            if (empty($paciente->meet_link)) {
                // Generar link placeholder único
                $paciente->meet_link = 'https://meet.google.com/lookup/' . \Illuminate\Support\Str::random(10);
            }
        });
    }
    
    // Logic: If precio_personalizado is set, use it. Otherwise, use global base price.
    public function getPrecioSesionAttribute()
    {
        if (!is_null($this->precio_personalizado)) {
            return $this->precio_personalizado;
        }
        
        return \App\Models\Setting::get('precio_base_sesion', 25000);
    }

    public function credits()
    {
        return $this->hasMany(PatientCredit::class, 'paciente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
