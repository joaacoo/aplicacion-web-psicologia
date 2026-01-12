<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'mensaje',
        'link',
        'leido'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
