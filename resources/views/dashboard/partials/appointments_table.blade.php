@if(isset($appointments) && $appointments->count() > 0)
    @php
        $payableApptIds = [];
        $foundUnfinished = false;
        foreach($appointments as $appt) {
            $isFinished = $appt->estado === \App\Models\Appointment::ESTADO_FINALIZADO || ($appt->fecha_hora->copy()->addMinutes(45)->isPast());
            if (!$isFinished) {
                if (!$foundUnfinished) {
                    $payableApptIds[] = $appt->id ?? $appt->fecha_hora->timestamp;
                    $foundUnfinished = true;
                }
            } else {
                $payableApptIds[] = $appt->id ?? $appt->fecha_hora->timestamp;
            }
        }
    @endphp
    <style>
        @media (max-width: 768px) {
            .appointments-table {
                display: none;
            }
            .appointments-cards {
                display: block;
            }
            .cancel-btn {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
            .join-btn {
                font-size: 0.9rem !important;
                font-weight: 800 !important;
                height: 38px !important;
            }
        }
        @media (min-width: 769px) {
            .appointments-table {
                display: block;
            }
            .appointments-cards {
                display: none;
            }
        }
        @media (max-width: 480px) {
            .disabled-btn {
                font-size: 0.5rem !important;
            }
        }
        .appointment-card {
            border: 2px solid #000;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fff;
            box-shadow: 4px 4px 0px #000;
        }
        .appointment-card .card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .appointment-card .card-label {
            font-weight: bold;
            font-size: 0.8rem;
            color: #666;
        }
        .appointment-card .card-value {
            font-size: 0.9rem;
        }
        .appointment-card .actions-wrapper {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .disabled-btn {
            opacity: 0.5;
            pointer-events: none;
            filter: grayscale(1);
        }
    </style>
    <div style="width: 100%; flex-grow: 1;">
        <!-- Desktop Table -->
        <div class="appointments-table">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-family: 'Inter', sans-serif;">
                <thead>
                    <tr style="border-bottom: 3px solid #000;">
                        <th style="text-align: center; padding: 0.5rem; font-weight: 800;">Fecha</th>
                        <th style="text-align: center; padding: 0.5rem; font-weight: 800;">Hora</th>
                        <th style="text-align: center; padding: 0.5rem; font-weight: 800;">Modalidad</th>
                        <th style="text-align: center; padding: 0.5rem; font-weight: 800; white-space: nowrap;">Estado Turno</th>
                        <th style="text-align: center; padding: 0.5rem; font-weight: 800;">Pago</th>
                        <th style="text-align: center; padding: 0.5rem; font-weight: 800;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appt)
                        @php
                            $isVirtual = ($appt->modalidad ?? 'virtual') == 'virtual';
                            $canJoin = $appt->canJoinMeet();
                            $isFinished = $appt->estado === \App\Models\Appointment::ESTADO_FINALIZADO || ($appt->fecha_hora->copy()->addMinutes(45)->isPast());
                            $isCriticalZone = $appt->isInCriticalZone();
                            
                            $paymentStatus = $appt->payment->estado ?? 'pendiente_pago';
                            $isPaid = $paymentStatus === 'verificado';
                            $paymentPending = $paymentStatus === 'pendiente';
                            $paymentRejected = $paymentStatus === 'rechazado';
                            
                            $uiStatus = $appt->ui_status ?? 'locked_sequential';
                            $isCreditApplied = $uiStatus === 'credit_applied';
                            $canPayThisOne = $uiStatus === 'payable';
                            
                            // REGLAS CRÍTICAS DE UI
                            $showPayBtn = false;
                            $showCancelBtn = false;
                            
                            if (!$isFinished && $appt->estado != 'cancelado') {
                                // 1. Botón Pagar: Solo si no está pago ni en revisión ni cubierto por crédito
                                if (!$isPaid && !$paymentPending && !$isCreditApplied) {
                                    $showPayBtn = true;
                                }
                                
                                // 2. Botón Cancelar:
                                if ($isPaid) {
                                    // Si está pago, siempre puede cancelar (genera crédito)
                                    $showCancelBtn = true;
                                } elseif ($isCriticalZone) {
                                    // REGLA: < 24hs -> OCULTAR CANCELAR (Incluso si está pago, el usuario no debería cancelar a último momento? No, el usuario dijo: "Si el turno NO está pago -> solo se permite pagar. No se permite cancelar ni recuperar.")
                                    // Si está PAGO y < 24hs, ¿debería poder cancelar? 
                                    // La regla dice: "Dentro de las 24 horas: Si el turno NO está pago -> solo se permite pagar. No se permite cancelar ni recuperar."
                                    // Implica que si ESTÁ PAGO sí podría cancelar (generando crédito/sesión perdida según política anterior, pero esta nueva regla es más restrictiva).
                                    // Vamos a ser estrictos: Dentro de las 24hs NO se permite cancelar ni recuperar si NO está pago.
                                    $showCancelBtn = $isPaid; 
                                } else {
                                    // > 24hs y No pago -> Puede cancelar
                                    $showCancelBtn = true;
                                }
                                
                                // 3. Pago en revisión -> OCULTAR AMBOS
                                if ($paymentPending) {
                                    $showPayBtn = false;
                                    $showCancelBtn = false;
                                }
                            }

                            // Mensaje de cancelación
                            if ($isPaid) {
                                $cancelMsg = '¿Seguro querés cancelar este turno? El pago fue verificado, así que se generará un crédito a tu favor automáticamente.';
                            } elseif ($paymentPending) {
                                $cancelMsg = 'Tu comprobante está siendo verificado. Si cancelás ahora, el análisis quedará pendiente.';
                            } else {
                                $cancelMsg = '¿Seguro querés cancelar este turno?' . ($isCriticalZone ? ' Al faltar menos de 24hs, se considerará sesión perdida.' : '');
                            }

                            // REGLAS RECUPERAR:
                            // Visible si: horas > 24 OR (horas ≤ 24 AND pagado)
                            // Oculto si: horas ≤ 24 AND no pagado
                            $showRecoverBtn = false;
                            $hasPendingRecovery = in_array($appt->id, $pendingRecoveryIds ?? []);
                            if ($appt->estado === 'cancelado' && $appt->fecha_hora->isFuture() && (!$isCriticalZone || $isPaid) && !$hasPendingRecovery) {
                                $showRecoverBtn = true;
                            }
                        @endphp
                        <tr style="border-bottom: 1px solid #2D2D2D;">
                            <td style="padding: 0.5rem; white-space: nowrap; text-align: center;">{{ $appt->fecha_hora->format('d/m') }}</td>
                            <td style="padding: 0.5rem; white-space: nowrap; text-align: center;">{{ $appt->fecha_hora->format('H:i') }}</td>
                            <td style="padding: 0.5rem; text-align: center;">
                                {{ ucfirst($appt->modalidad ?? 'Virtual') }}
                            </td>
                            <td style="padding: 0.5rem; white-space: nowrap;">
                                @php
                                    $statusName = match($appt->estado) {
                                        'confirmado' => 'Confirmado',
                                        'cancelado' => 'Cancelado',
                                        \App\Models\Appointment::ESTADO_FINALIZADO => 'Finalizado',
                                        default => ucfirst($appt->estado)
                                    };
                                    $statusBg = match(true) {
                                        $appt->estado === 'confirmado' => '#f0fdf4',
                                        $appt->estado === 'cancelado' => '#fff1f2',
                                        $appt->estado === \App\Models\Appointment::ESTADO_FINALIZADO => '#f1f5f9',
                                        $appt->estado === \App\Models\Appointment::ESTADO_RECUPERADO => '#eef2ff',
                                        default => '#fffbeb'
                                    };
                                    $statusColor = match(true) {
                                        $appt->estado === 'confirmado' => '#166534',
                                        $appt->estado === 'cancelado' => '#e11d48',
                                        $appt->estado === \App\Models\Appointment::ESTADO_FINALIZADO => '#475569',
                                        $appt->estado === \App\Models\Appointment::ESTADO_RECUPERADO => '#4338ca',
                                        default => '#92400e'
                                    };
                                @endphp
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 2px;">
                                    <span style="font-weight: 900; color: {{ $statusColor }}; background: {{ $statusBg }}; border: 1px solid {{ $statusColor }}; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; display: inline-block; text-transform: uppercase; white-space: nowrap;">
                                        {{ $statusName }}
                                    </span>
                                    @if($appt->es_recurrente)
                                        <div style="font-size: 0.75rem; color: #000; font-weight: 900; text-transform: uppercase; display: flex; align-items: center; gap: 3px; white-space: nowrap;">
                                            <i class="fa-solid fa-repeat" style="color: #6366f1;"></i> Fijo
                                        </div>
                                    @endif
                                    @if($appt->notas === 'Asignado por la Lic.' || str_contains($appt->notas ?? '', 'Turno de recuperación'))
                                        <div style="font-size: 0.65rem; color: #4338ca; font-weight: 800; text-transform: uppercase; margin-top: 2px;">
                                            Asignado por la Lic.
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 0.5rem; text-align: center;">
                                @if($isPaid)
                                    <span style="color: green; font-weight: bold;">Verificado</span>
                                @elseif($isCreditApplied)
                                    <span style="color: #4338ca; font-weight: bold;">Crédito</span>
                                @elseif($paymentPending)
                                    <span style="color: grey; font-weight: bold;">En Revisión</span>
                                @elseif($paymentRejected)
                                    <span style="color: #e11d48; font-weight: bold;">Rechazado</span>
                                @else
                                    <span style="color: #666; font-weight: bold;">Pendiente</span>
                                @endif
                            </td>
                            <td style="padding: 0.5rem;">
                                @if($isFinished)
                                    <div style="text-align: center; color: #666; font-weight: 900;">—</div>
                                @elseif($appt->estado != 'cancelado' || $showRecoverBtn || $hasPendingRecovery)
                                    <div class="actions-wrapper" style="display: flex; gap: 5px; flex-wrap: wrap; justify-content: center;">
                                        
                                        {{-- JOIN BUTTON --}}
                                        @if(($isPaid || $paymentPending || $isCreditApplied) && $isVirtual && !$hasPendingRecovery && $appt->estado != 'cancelado')
                                            <a href="{{ $appt->meet_link ?: '#' }}" 
                                               target="_blank" 
                                               class="neobrutalist-btn join-btn {{ $canJoin ? 'bg-celeste' : 'disabled-btn' }}" 
                                               style="flex: 1 1 0%; padding: 0.3rem 0.6rem; font-size: 0.75rem; border: 2px solid #000; text-decoration: none; color: #000; display: inline-flex; align-items: center; gap: 4px; justify-content: center; min-width: 90px; height: 32px; white-space: nowrap;"
                                               data-start="{{ $appt->fecha_hora->toISOString() }}">
                                                <i class="fa-solid fa-video"></i> Unirse
                                            </a>
                                        @endif

                                        {{-- PAY BUTTON --}}
                                        @if($showPayBtn)
                                            <button onclick="openPaymentModal({{ $appt->id ?? 0 }}, {{ auth()->user()->paciente->precio_sesion ?? $appt->monto_final }})" 
                                                    class="neobrutalist-btn bg-verde {{ $canPayThisOne ? '' : 'disabled-btn' }}"
                                                    {{ $canPayThisOne ? '' : 'disabled' }}
                                                    style="flex: 1 1 0%; padding: 0.3rem 0.6rem; font-size: 0.75rem; min-width: 90px; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;"
                                                    title="{{ $appt->payment_block_reason }}">
                                                <i class="fa-solid fa-dollar-sign"></i> Pagar
                                            </button>
                                        @endif

                                        {{-- CANCEL BUTTON --}}
                                        @if($showCancelBtn)
                                            <form action="{{ $appt->id ? route('appointments.cancel', $appt->id) : route('appointments.cancelProjected') }}" method="POST" style="display:inline; flex: 1 1 0%; min-width: 90px;">
                                                @csrf
                                                @if(!$appt->id) <input type="hidden" name="date" value="{{ $appt->fecha_hora->format('Y-m-d H:i:s') }}"> @endif
                                                <button type="button" class="neobrutalist-btn bg-lila cancel-btn" 
                                                        style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;" 
                                                        onclick="window.showConfirm('{{ $cancelMsg }}', () => { window.showProcessing('Cancelando turno...'); this.closest('form').submit(); })">
                                                    Cancelar
                                                </button>
                                            </form>
                                        @endif

                                        
                                        @if($paymentPending)
                                            <div style="flex: 1 1 100%; text-align: center; font-size: 0.7rem; color: #666; font-weight: 800; margin-top: 4px;">
                                                <i class="fa-solid fa-clock"></i> Pago en revisión
                                            </div>
                                        @endif

                                        {{-- RECOVER BUTTON --}}
                                        @if($showRecoverBtn)
                                            <button onclick="openRecoveryModal({{ $appt->id }})" 
                                                    class="neobrutalist-btn bg-amarillo"
                                                    style="flex: 1 1 0%; padding: 0.3rem 0.6rem; font-size: 0.75rem; min-width: 90px; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;">
                                                <i class="fa-solid fa-rotate-left"></i> Recuperar
                                            </button>
                                        @endif

                                        @if($hasPendingRecovery)
                                            <button class="neobrutalist-btn disabled-btn" 
                                                    style="flex: 1 1 100%; padding: 0.3rem 0.6rem; font-size: 0.6rem; min-height: 32px; height: auto; white-space: normal; text-align: center; line-height: 1.2; display: inline-flex; align-items: center; justify-content: center; gap: 5px; background: #fffadc; border: 2px solid #000; color: #92400e; font-weight: 800; cursor: default;">
                                                <i class="fa-solid fa-hourglass-half" style="flex-shrink: 0;"></i> Recuperación enviada
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="appointments-cards">
            @foreach($appointments as $appt)
                @php
                    $isVirtual = ($appt->modalidad ?? 'virtual') == 'virtual';
                    $canJoin = $appt->canJoinMeet();
                    $isFinished = $appt->estado === \App\Models\Appointment::ESTADO_FINALIZADO || ($appt->fecha_hora->copy()->addMinutes(45)->isPast());
                    $isCriticalZone = $appt->isInCriticalZone();
                    
                    $paymentStatus = $appt->payment->estado ?? 'pendiente_pago';
                    $isPaid = $paymentStatus === 'verificado';
                    $paymentPending = $paymentStatus === 'pendiente';
                    $paymentRejected = $paymentStatus === 'rechazado';
                    
                    $uiStatus = $appt->ui_status ?? 'locked_sequential';
                    $isCreditApplied = $uiStatus === 'credit_applied';
                    $canPayThisOne = $uiStatus === 'payable';

                    $showPayBtn = false;
                    $showCancelBtn = false;
                    
                    if (!$isFinished && $appt->estado != 'cancelado') {
                        if (!$isPaid && !$paymentPending && !$isCreditApplied) $showPayBtn = true;
                        
                        if ($isPaid) $showCancelBtn = true;
                        elseif ($isCriticalZone) $showCancelBtn = false; 
                        else $showCancelBtn = true;
                        
                        if ($paymentPending) { $showPayBtn = false; $showCancelBtn = false; }
                    }

                    if ($isPaid) {
                        $cancelMsg = '¿Seguro querés cancelar? El pago fue verificado, se generará un crédito a tu favor.';
                    } else {
                        $cancelMsg = '¿Seguro querés cancelar este turno?' . ($isCriticalZone ? ' Al faltar menos de 24hs, se considerará sesión perdida.' : '');
                    }

                    $showRecoverBtn = false;
                    $hasPendingRecovery = in_array($appt->id, $pendingRecoveryIds ?? []);
                    if ($appt->estado === 'cancelado' && $appt->fecha_hora->isFuture() && (!$isCriticalZone || $isPaid) && !$hasPendingRecovery) {
                        $showRecoverBtn = true;
                    }
                @endphp
                <div class="appointment-card">
                    <div class="card-row">
                        <span class="card-label">Fecha:</span>
                        <span class="card-value">{{ $appt->fecha_hora->format('d/m') }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Hora:</span>
                        <span class="card-value">{{ $appt->fecha_hora->format('H:i') }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Modalidad:</span>
                        <span class="card-value">{{ ucfirst($appt->modalidad ?? 'Virtual') }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Estado:</span>
                        <span class="card-value">
                            @php
                                $statusName = match($appt->estado) {
                                    'confirmado' => 'Confirmado',
                                    'cancelado' => 'Cancelado',
                                    \App\Models\Appointment::ESTADO_SESION_PERDIDA => 'Sesión Perdida',
                                    \App\Models\Appointment::ESTADO_FINALIZADO => 'Finalizado',
                                    default => ucfirst($appt->estado)
                                };
                                $statusBg = match($appt->estado) {
                                    'confirmado' => '#f0fdf4',
                                    'cancelado' => '#fef2f2',
                                    \App\Models\Appointment::ESTADO_SESION_PERDIDA => '#fff1f2',
                                    \App\Models\Appointment::ESTADO_RECUPERADO => '#eef2ff',
                                    default => '#fffbeb'
                                };
                                $statusColor = match($appt->estado) {
                                    'confirmado' => '#166534',
                                    'cancelado' => '#991b1b',
                                    \App\Models\Appointment::ESTADO_SESION_PERDIDA => '#e11d48',
                                    \App\Models\Appointment::ESTADO_FINALIZADO => '#475569',
                                    \App\Models\Appointment::ESTADO_RECUPERADO => '#4338ca',
                                    default => '#92400e'
                                };
                            @endphp
                            <span style="font-weight: 900; color: {{ $statusColor }}; background: {{ $statusBg }}; border: 1px solid {{ $statusColor }}; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; display: inline-block; text-transform: uppercase; white-space: nowrap;">
                                {{ $statusName }}
                            </span>
                            @if($appt->es_recurrente && !($appt->recuperada ?? false))
                                <div style="font-size: 0.75rem; color: #000; font-weight: 900; text-transform: uppercase; display: flex; align-items: center; gap: 3px; white-space: nowrap; margin-top: 4px;">
                                    <i class="fa-solid fa-repeat" style="color: #6366f1;"></i> Fijo
                                </div>
                            @endif
                            @if($appt->notas === 'Asignado por la Lic.' || str_contains($appt->notas ?? '', 'Turno de recuperación'))
                                <div style="font-size: 0.7rem; color: #4338ca; font-weight: 800; text-transform: uppercase; margin-top: 4px;">
                                    Asignado por la Lic.
                                </div>
                            @endif
                        </span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Pago:</span>
                        <span class="card-value">
                            @if($isPaid) <span style="color: green; font-weight: bold;">Verificado</span>
                            @elseif($isCreditApplied) <span style="color: #4338ca; font-weight: bold;">Crédito</span>
                            @elseif($paymentPending) <span style="color: grey; font-weight: bold;">En Revisión</span>
                            @elseif($paymentRejected) <span style="color: #e11d48; font-weight: bold;">Rechazado</span>
                            @else <span style="color: #666; font-weight: bold;">Pendiente</span>
                            @endif
                        </span>
                    </div>
                    <div class="actions-wrapper">
                        @if(!$isFinished && ($appt->estado != 'cancelado' || $showRecoverBtn || $hasPendingRecovery))
                            @if(($isPaid || $paymentPending || $isCreditApplied) && $isVirtual && !$hasPendingRecovery && $appt->estado != 'cancelado')
                                <a href="{{ $appt->meet_link ?: '#' }}" target="_blank" class="neobrutalist-btn join-btn {{ $canJoin ? 'bg-celeste' : 'disabled-btn' }}" style="flex: 1; min-height: 38px; height: auto; padding: 0.4rem; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #000;" data-start="{{ $appt->fecha_hora->toISOString() }}">
                                    <i class="fa-solid fa-video"></i> Unirse
                                </a>
                            @endif
                            @if($showPayBtn)
                                <button onclick="openPaymentModal({{ $appt->id ?? 0 }}, {{ auth()->user()->paciente->precio_sesion ?? $appt->monto_final }})" class="neobrutalist-btn bg-verde {{ $canPayThisOne ? '' : 'disabled-btn' }}" {{ $canPayThisOne ? '' : 'disabled' }} style="flex: 1; min-height: 38px; height: auto; padding: 0.4rem; font-size: 0.9rem;" title="{{ $appt->payment_block_reason }}">
                                    <i class="fa-solid fa-dollar-sign"></i> Pagar
                                </button>
                            @endif
                            @if($showCancelBtn)
                                <form action="{{ $appt->id ? route('appointments.cancel', $appt->id) : route('appointments.cancelProjected') }}" method="POST" style="flex: 1;">
                                    @csrf
                                    @if(!$appt->id) <input type="hidden" name="date" value="{{ $appt->fecha_hora->format('Y-m-d H:i:s') }}"> @endif
                                    <button type="button" class="neobrutalist-btn bg-lila cancel-btn" style="width: 100%; min-height: 38px; height: auto; padding: 0.4rem; font-size: 0.9rem; display: flex; align-items: center; justify-content: center;" onclick="window.showConfirm('{{ $cancelMsg }}', () => { window.showProcessing('Cancelando turno...'); this.closest('form').submit(); })">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                            @if($isCreditApplied && !$isPaid)
                                <div style="width: 100%; text-align: center; font-size: 0.7rem; color: #4338ca; font-weight: 800;">Cubierto con crédito</div>
                            @endif
                            @if($paymentPending)
                                <div style="width: 100%; text-align: center; font-size: 0.7rem; color: #666; font-weight: 800;"><i class="fa-solid fa-clock"></i> Pago en revisión</div>
                            @endif

                            @if($showRecoverBtn)
                                <button onclick="openRecoveryModal({{ $appt->id }})" 
                                        class="neobrutalist-btn bg-amarillo"
                                        style="flex: 1; min-height: 38px; height: auto; padding: 0.4rem; font-size: 0.9rem; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-rotate-left"></i> Recuperar
                                </button>
                            @endif

                            @if($hasPendingRecovery)
                                <button class="neobrutalist-btn disabled-btn" 
                                        style="width: 100%; padding: 0.4rem 0.6rem; font-size: 0.6rem; min-height: 32px; height: auto; white-space: normal; text-align: center; line-height: 1.2; display: flex; align-items: center; justify-content: center; gap: 5px; background: #fffadc; border: 2px solid #000; color: #92400e; font-weight: 800; cursor: default; margin-top: 4px;">
                                    <i class="fa-solid fa-hourglass-half" style="flex-shrink: 0;"></i> Recuperación enviada
                                </button>
                            @endif
@else
                           <div style="width: 100%; text-align: center; color: #666;">—</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(method_exists($appointments, 'onFirstPage'))
        <div class="pagination-container" style="display: flex; justify-content: center; gap: 0.8rem; margin-top: auto; padding-top: 1.5rem; align-items: center; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 0.5rem; width: 100%;">
            @if($appointments->onFirstPage())
                <span class="neobrutalist-btn pagination-mobile-btn" style="background: #eee; cursor: not-allowed; opacity: 0.6; box-shadow: none;">Anterior</span>
            @else
                <a href="{{ $appointments->previousPageUrl() }}#mis-turnos" class="neobrutalist-btn bg-amarillo pagination-mobile-btn" style="text-decoration: none; color: #000;">Anterior</a>
            @endif
        
            <span class="pagination-mobile-indicator" style="font-weight: 800; font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #000; padding: 0.4rem 0.2rem; white-space: nowrap;">
                {{ $appointments->currentPage() }} / {{ $appointments->lastPage() }}
            </span>

            @if($appointments->hasMorePages())
                <a href="{{ $appointments->nextPageUrl() }}#mis-turnos" class="neobrutalist-btn bg-amarillo pagination-mobile-btn" style="text-decoration: none; color: #000;">Siguiente</a>
            @else
                <span class="neobrutalist-btn pagination-mobile-btn" style="background: #eee; cursor: not-allowed; opacity: 0.6; box-shadow: none;">Siguiente</span>
            @endif
        </div>
        <div class="pagination-container" style="display: flex; justify-content: center; gap: 0.8rem; margin-top: auto; padding-top: 1.5rem; align-items: center; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 0.5rem; width: 100%;">
            <span style="font-weight: 800; font-family: 'Inter', sans-serif; font-size: 0.85rem; color: #666;">
                Mostrando {{ $appointments->count() }} turno(s)
            </span>
        </div>
        @endif

        {{-- Toggle: Ver todos / Ver menos --}}
        <style>
            @media (max-width: 768px) {
                .toggle-view-btn-mobile {
                    width: 90% !important;
                    display: inline-block !important;
                    padding: 0.4rem 0.5rem !important;
                    font-size: 0.70rem !important;
                    letter-spacing: -0.5px !important;
                    margin: 0 auto;
                }
                .pagination-mobile-btn {
                    padding: 0.4rem 0.6rem !important;
                    font-size: 0.75rem !important;
                }
                .pagination-mobile-indicator {
                    font-size: 0.8rem !important;
                    padding: 0.4rem 0.1rem !important;
                }
                .pagination-container {
                    gap: 0.4rem !important;
                }
            }
        </style>
        
        @if(!request()->has('ver_todo'))
            <div style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('patient.dashboard', ['ver_todo' => 1]) }}#mis-turnos" 
                   class="neobrutalist-btn bg-white toggle-view-btn toggle-view-btn-mobile"
                   style="font-size: 0.75rem; padding: 0.5rem 1rem; text-decoration: none; color: #000; font-weight: 800; border: 2px solid #000; cursor: pointer;">
                    <i class="fa-solid fa-eye"></i> VER TODOS LOS TURNOS DEL AÑO
                </a>
            </div>
        @else
            <div style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('patient.dashboard') }}#mis-turnos" 
                   class="neobrutalist-btn bg-white toggle-view-btn toggle-view-btn-mobile"
                   style="font-size: 0.75rem; padding: 0.5rem 1rem; text-decoration: none; color: #000; font-weight: 800; border: 2px solid #000; cursor: pointer;">
                    <i class="fa-solid fa-eye-slash"></i> VER MENOS TURNOS
                </a>
            </div>
        @endif

    <script>
        // Lógica de "Botón Inteligente" local
        if (!window.appointmentsInterval) {
            window.appointmentsInterval = setInterval(() => {
                const now = new Date();
                document.querySelectorAll('.join-btn').forEach(btn => {
                    const startTime = new Date(btn.dataset.start);
                    const diffMs = startTime - now;
                    const diffMin = diffMs / 1000 / 60;
                    
                    // Read payment status and estado from data attribute
                    const isPaid = btn.getAttribute('data-paid') === 'true';
                    const estado = btn.getAttribute('data-estado') || 'confirmado';

                    // Si faltan 10 min o menos, y no pasaron 45 min de la hora de inicio, el botón se habilita sin importar el pago ni el estado.
                    // Si faltan MÁS de 10 min, queda desactivado.
                    if (diffMin <= 10 && diffMin > -45) {
                        btn.classList.remove('disabled-btn');
                        btn.classList.add('bg-celeste');
                    } else {
                        btn.classList.add('disabled-btn');
                        btn.classList.remove('bg-celeste');
                    }
                });
            }, 60000); // Cada 1 minuto
        }

        // Toggle Ver todos / Ver menos con AJAX (usando delegación de eventos)
        if (!window.appointmentsAjaxBound) {
            window.appointmentsAjaxBound = true;
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.toggle-view-btn, .pagination-mobile-btn, .reset-filters-btn');
                if (btn && btn.tagName === 'A' && btn.getAttribute('href')) {
                    e.preventDefault();
                    const url = btn.getAttribute('href');
                    
                    fetchAndRefreshTable(url);
                    window.history.pushState({}, '', url);
                }
            });

            // Filter form AJAX submission
            document.addEventListener('submit', function(e) {
                const form = e.target.closest('#appointments-filter-form');
                if (form) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData);
                    // Retain ver_todo if it exists in current URL
                    const currentUrl = new URL(window.location.href);
                    if (currentUrl.searchParams.has('ver_todo')) {
                        params.append('ver_todo', currentUrl.searchParams.get('ver_todo'));
                    }
                    const url = form.action + '?' + params.toString();
                    
                    fetchAndRefreshTable(url);
                    
                    // Update URL without reloading to allow sharing the filtered state
                    window.history.pushState({}, '', url);
                }
            });

            // Helper function for AJAX fetch
            function fetchAndRefreshTable(url) {
                const container = document.getElementById('appointments-table-container');
                if (container) {
                    const loading = document.getElementById('appointments-loading');
                    if (loading) loading.style.display = 'block';
                    container.style.opacity = '0.5';
                    
                    // Add AJAX parameter so the controller knows to return only the table
                    const fetchUrl = new URL(url, window.location.origin);
                    fetchUrl.searchParams.append('ajax', '1');

                    fetch(fetchUrl.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        if (loading) loading.style.display = 'none';
                        container.style.opacity = '1';
                        // Scroll to table smoothly
                        document.getElementById('mis-turnos')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    })
                    .catch(err => {
                        console.error(err);
                        if (loading) loading.style.display = 'none';
                        container.style.opacity = '1';
                        // Fallback: reload page
                        window.location.href = url;
                    });
                } else {
                    window.location.href = url;
                }
            }
        }
    </script>

@else
    <div style="flex-grow: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 3rem; border: 3px dashed #000; border-radius: 12px; background: #fafafa; margin: 1rem 0;">
        <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
        <p style="font-weight: 800; color: #000; text-align: center; font-size: 1.1rem; margin-bottom: 0;">No hay turnos que coincidan con tu búsqueda.</p>
    </div>
@endif
