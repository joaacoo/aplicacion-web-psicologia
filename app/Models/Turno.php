<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turno extends Model
{
    use SoftDeletes;
    /**
     * Relationship to Clinical History (one note per turno)
     */
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_hora' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship to Clinical History (one note per turno)
     */
    public function clinicalHistory()
    {
        return $this->hasOne(ClinicalHistory::class, 'turno_id');
    }
}
