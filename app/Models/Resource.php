<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Added this line for the relationship

class Resource extends Model
{
    protected $fillable = [
        'paciente_id',
        'title',
        'description',
        'file_path',
        'file_type'
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    //
}
