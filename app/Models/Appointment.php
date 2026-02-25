<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;
    
    const ESTADO_CONFIRMADO = 'confirmado';
    const ESTADO_CANCELADO = 'cancelado';
    const ESTADO_FINALIZADO = 'finalizado';
    const ESTADO_SESION_PERDIDA = 'sesion_perdida';
    const ESTADO_RECUPERADO = 'recuperado';
    
    protected $table = 'turnos';

    protected $fillable = [
        'usuario_id',
        'fecha_hora',
        'estado', // pendiente, confirmado, cancelado, completado
        'es_recurrente',
        'notas',
        'modalidad',
        'link_reunion',
        'vence_en',
        'notificado_recordatorio_en',
        'notificado_ultimatum_en',
        'notificado_una_hora_en',
        'debe_pagarse',
        'paciente_id',
        'monto_final',
        'frecuencia',
        'estado_realizado',
        'motivo_cancelacion',
        'cancelado_por',
        'estado_pago',
        'es_recuperacion',
        'waitlist_id',
        'ui_status',
        'cancelado_con_mas_de_24hs',
        'recovery_requested_at',
    ];

    /**
     * Determina si el turno genera deuda.
     * Es deuda si está confirmado (y no pagado) O si está cancelado pero se cobra igual.
     */
    public function generaDeuda(): bool
    {
        return $this->estado === 'confirmado' || ($this->estado === 'cancelado' && $this->debe_pagarse);
    }

    protected $casts = [
        'fecha_hora' => 'datetime',
        'es_recurrente' => 'boolean',
        'es_recuperacion' => 'boolean',
        'cancelado_con_mas_de_24hs' => 'boolean',
        'vence_en' => 'datetime',
        'notificado_una_hora_en' => 'datetime',
        'recovery_requested_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    protected static function booted()
    {
        static::creating(function ($turno) {
            // Ensure patient relationship is loaded or accessible
            // If created via $paciente->turnos()->create(), the relation might be set.
            // But usually we just have usuario_id or paciente_id.
            
            // If paciente_id is set, use it. If not, try to deduce from usuario_id
            $paciente = null;
            if ($turno->paciente_id) {
                $paciente = Paciente::find($turno->paciente_id);
            } elseif ($turno->usuario_id) {
                $paciente = Paciente::where('user_id', $turno->usuario_id)->first();
                if ($paciente) {
                    $turno->paciente_id = $paciente->id;
                }
            }

            if ($paciente) {
                $turno->monto_final = $turno->paciente_id ? $paciente->honorario_pactado : 0;
                
                // Fallback to global setting if 0 or null
                if (!$turno->monto_final) {
                    $turno->monto_final = \App\Models\Setting::get('precio_base_sesion', 25000);
                }

                if ($paciente->tipo_paciente === 'nuevo') {
                    $turno->debe_pagarse = true;
                    // Vence 1 día antes a la misma hora
                    if ($turno->fecha_hora) {
                         $turno->vence_en = $turno->fecha_hora->copy()->subDay();
                    }
                } else {
                    $turno->debe_pagarse = false;
                }
            }
        });
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'turno_id');
    }

    /**
     * Accessor para obtener el link de la reunión.
     * Prioriza el link específico del turno (link_reunion) y falla al link del paciente.
     */
    public function getMeetLinkAttribute()
    {
        if ($this->link_reunion) {
            return $this->link_reunion;
        }

        return $this->user->paciente->meet_link ?? '#';
    }

    // ═══════════════════════════════════════════════════════════
    // LÓGICA: ¿ESTA SESIÓN ES PAGABLE AHORA?
    // ═══════════════════════════════════════════════════════════

    /**
     * ¿Puede el paciente PAGAR esta sesión AHORA?
     * Se puede pagar si:
     * - No está pagado ni verificado.
     * - No está cancelado.
     * - No es un turno de recuperación (no genera nuevo pago).
     * - No es pasado.
     * - Faltan más de 24hs.
     * - NO tiene créditos activos (el crédito manda).
     */
    public function isPayable(?bool $hasCredit = null): bool
    {
        // Los turnos de recuperación nunca deberían generar un nuevo pago
        if ($this->es_recuperacion) return false;

        if ($this->estado_pago === 'verificado') return false;
        if ($this->estado === 'cancelado') return false;
        if ($this->fecha_hora->isPast()) return false;

        $horasHasta = $this->fecha_hora->diffInHours(now());
        if ($horasHasta < 24) return false;

        // Si explícitamente se pasa que tiene crédito, no es pagable
        if ($hasCredit === true) return false;

        return true;
    }


    /**
     * Obtener razón por la que NO es pagable
     */
    public function getPaymentBlockReason(): ?string
    {
        if ($this->estado_pago === 'verificado') return 'Pagado ✅';
        if ($this->es_recuperacion) return 'Turno de recuperación (no genera nuevo pago).';
        if ($this->estado === 'cancelado') return 'Cancelado';
        if ($this->fecha_hora->isPast()) return 'Sesión pasada';

        $horasHasta = $this->fecha_hora->diffInHours(now());
        if ($horasHasta < 24) {
            return '❌ Pasó el plazo de pago (24hs antes). Esta sesión se cancelará automáticamente.';
        }

        return null;
    }

    /**
     * Horas restantes PARA PAGAR (hasta 24h antes)
     */
    public function getHoursUntilPaymentDeadline(): ?int
    {
        if ($this->fecha_hora->isPast()) return null;
        $horasHasta = $this->fecha_hora->diffInHours(now());
        return max(0, $horasHasta - 24);
    }

    /**
     * Cuándo VENCE el plazo de pago (24h antes de sesión)
     */
    public function getPaymentDeadline()
    {
        return $this->fecha_hora->copy()->subHours(24);
    }

    // ═════════════════════════════════════════════════════════==
    /**
     * Check if the associated payment is verified.
     */
    public function paymentWasVerified(): bool
    {
        return $this->payment && $this->payment->estado === 'verificado';
    }

    // ═══════════════════════════════════════════════════════════
    // LÓGICA: ESTADO DE REALIZACIÓN
    // ═══════════════════════════════════════════════════════════

    public function isPastSessionTime(): bool
    {
        return $this->fecha_hora->isPast();
    }

    public function canJoinMeet()
    {
        $ahora = now();
        $inicio = $this->fecha_hora->copy()->subMinutes(10);
        $fin = $this->fecha_hora->copy()->addMinutes(45);

        return $ahora->between($inicio, $fin);
    }

    public function isInCriticalZone()
    {
        // Retorna true si faltan menos de 24hs para el inicio del turno
        return now()->diffInHours($this->fecha_hora, false) < 24;
    }

    public function isRealizado(): bool
    {
        return $this->estado === 'completado' || 
               $this->estado_realizado === 'realizado';
    }

    public function markAsRealizado(): void
    {
        if ($this->isPastSessionTime()) {
            $this->update([
                'estado' => 'completado',
                'estado_realizado' => 'realizado',
            ]);
        }
    }
}
