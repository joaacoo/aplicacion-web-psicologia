<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'user_id',
        'tipo_paciente',
        'honorario_pactado',
        'precio_personalizado', // New column
        'notas_vinculo',
        'telefono',
    ];

    protected $casts = [
        'telefono' => 'encrypted',
        'precio_personalizado' => 'decimal:2',
    ];
    
    // Logic: If precio_personalizado is set, use it. Otherwise, use global base price.
    public function getPrecioSesionAttribute()
    {
        if (!is_null($this->precio_personalizado)) {
            return $this->precio_personalizado;
        }
        
        return \App\Models\Setting::get('precio_base_sesion', 25000);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
