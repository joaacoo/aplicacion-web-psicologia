<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClinicalHistory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'turno_id',
        'paciente_id',
        'content',
    ];

    protected $casts = [
        'content' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship to Turno (appointment)
     */
    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    /**
     * Relationship to Paciente
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}
