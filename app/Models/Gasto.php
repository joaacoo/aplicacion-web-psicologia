<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $fillable = [
        'user_id',
        'fecha',
        'categoria',
        'descripcion',
        'monto',
        'es_recurrente',
    ];

    protected $casts = [
        'fecha' => 'date',
        'es_recurrente' => 'boolean',
        'monto' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
