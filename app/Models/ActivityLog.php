<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'changes' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
