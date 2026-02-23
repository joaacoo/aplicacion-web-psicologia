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
        }
        @media (min-width: 769px) {
            .appointments-table {
                display: block;
            }
            .appointments-cards {
                display: none;
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
                            $canPayThisOne = in_array($appt->id ?? $appt->fecha_hora->timestamp, $payableApptIds);
                        @endphp
                        <tr style="border-bottom: 1px solid #2D2D2D;">
                            <td style="padding: 0.5rem; white-space: nowrap; text-align: center;">{{ $appt->fecha_hora->format('d/m') }}</td>
                            <td style="padding: 0.5rem; white-space: nowrap; text-align: center;">{{ $appt->fecha_hora->format('H:i') }}</td>
                            <td style="padding: 0.5rem; text-align: center;">
                                {{ ucfirst($appt->modalidad ?? 'Virtual') }}
                            </td>
                            <td style="padding: 0.5rem; white-space: nowrap;">
                                @php
                                    $isRecuperada = $appt->recuperada ?? false;
                                    $statusName = match($appt->estado) {
                                        'confirmado' => 'Confirmado',
                                        'cancelado' => 'Cancelado',
                                        'recuperada' => 'Recuperada',
                                        \App\Models\Appointment::ESTADO_SESION_PERDIDA => 'Sesión Perdida',
                                        \App\Models\Appointment::ESTADO_FINALIZADO => 'Finalizado',
                                        default => ucfirst($appt->estado)
                                    };
                                    $statusBg = match(true) {
                                        $isRecuperada => '#e0e7ff',
                                        $appt->estado === 'confirmado' => '#f0fdf4',
                                        $appt->estado === 'cancelado' => '#fef2f2',
                                        $appt->estado === \App\Models\Appointment::ESTADO_SESION_PERDIDA => '#fff1f2',
                                        $appt->estado === \App\Models\Appointment::ESTADO_FINALIZADO => '#f1f5f9',
                                        default => '#fffbeb'
                                    };
                                    $statusColor = match(true) {
                                        $isRecuperada => '#4338ca',
                                        $appt->estado === 'confirmado' => '#166534',
                                        $appt->estado === 'cancelado' => '#991b1b',
                                        $appt->estado === \App\Models\Appointment::ESTADO_SESION_PERDIDA => '#e11d48',
                                        $appt->estado === \App\Models\Appointment::ESTADO_FINALIZADO => '#475569',
                                        default => '#92400e'
                                    };
                                @endphp
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 2px;">
                                    <span style="font-weight: 900; color: {{ $statusColor }}; background: {{ $statusBg }}; border: 1px solid {{ $statusColor }}; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; display: inline-block; text-transform: uppercase; white-space: nowrap;">
                                        {{ $statusName }}
                                    </span>
                                    @if($appt->es_recurrente && !($appt->recuperada ?? false))
                                        <div style="font-size: 0.75rem; color: #000; font-weight: 900; text-transform: uppercase; display: flex; align-items: center; gap: 3px; white-space: nowrap;">
                                            <i class="fa-solid fa-repeat" style="color: #6366f1;"></i> Fijo
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 0.5rem; text-align: center;">
                                @if($appt->payment && $appt->payment->estado == 'verificado')
                                    <span style="color: green; font-weight: bold;">Verificado</span>
                                @elseif($appt->payment && $appt->payment->estado == 'pendiente')
                                    <span style="color: grey; font-weight: bold;">En Revisión</span>
                                @elseif($appt->payment && $appt->payment->estado == 'rechazado')
                                    <span style="color: #e11d48; font-weight: bold;">Rechazado</span>
                                @else
                                    <span style="color: #666; font-weight: bold;">Pendiente</span>
                                @endif
                            </td>
                            <td style="padding: 0.5rem;">
                                @if($isFinished)
                                    <div style="text-align: center; color: #666; font-weight: 900;">—</div>
                                @elseif($appt->estado != 'cancelado' && $appt->estado != \App\Models\Appointment::ESTADO_SESION_PERDIDA)
                                    <div class="actions-wrapper" style="display: flex; gap: 5px; flex-wrap: wrap; justify-content: center;">
                                        @if($isPaid)
                                            @if($isVirtual && $appt->estado != 'pendiente' && $appt->estado != 'cancelado')
                                                <a href="{{ $appt->meet_link ?: '#' }}" 
                                                   target="_blank" 
                                                   class="neobrutalist-btn join-btn {{ $canJoin ? 'bg-celeste' : 'disabled-btn' }}" 
                                                   style="flex: 1 1 0%; padding: 0.3rem 0.6rem; font-size: 0.75rem; border: 2px solid #000; text-decoration: none; color: #000; display: inline-flex; align-items: center; gap: 4px; justify-content: center; min-width: 90px; height: 32px; white-space: nowrap;"
                                                   data-start="{{ $appt->fecha_hora->toISOString() }}"
                                                   data-paid="true"
                                                   data-estado="{{ $appt->estado }}">
                                                    <i class="fa-solid fa-video"></i> Unirse
                                                </a>
                                            @endif
                                        @else
                                            @php
                                                $montoAPagar = auth()->user()->paciente->precio_sesion ?? $appt->monto_final;
                                            @endphp
                                            @if($canPayThisOne)
                                                <button onclick="openPaymentModal({{ $appt->id ?? 0 }}, {{ $montoAPagar }})" class="neobrutalist-btn bg-verde" style="flex: 1 1 0%; padding: 0.3rem 0.6rem; font-size: 0.75rem; min-width: 90px; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;">
                                                    <i class="fa-solid fa-dollar-sign"></i> Pagar
                                                </button>
                                            @else
                                                <button class="neobrutalist-btn bg-verde disabled-btn" disabled style="flex: 1 1 0%; padding: 0.3rem 0.6rem; font-size: 0.75rem; min-width: 90px; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;" title="Pagá la sesión anterior primero">
                                                    <i class="fa-solid fa-dollar-sign"></i> Pagar
                                                </button>
                                            @endif
                                        @endif

                                         @if($isPaid || !$isCriticalZone)
                                            @if($appt->id)
                                                <form action="{{ route('appointments.cancel', $appt->id) }}" method="POST" style="display:inline; flex: 1 1 0%; min-width: 90px;">
                                                    @csrf
                                                    @php
                                                        if ($isPaid) {
                                                            $cancelMsg = '¿Seguro querés cancelar este turno? Se generará crédito a tu favor.';
                                                        } else {
                                                            $cancelMsg = '¿Seguro querés cancelar este turno?';
                                                        }
                                                    @endphp
                                                    <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;" onclick="window.showConfirm('{{ $cancelMsg }}', () => this.closest('form').submit())">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('appointments.cancelProjected') }}" method="POST" style="display:inline; flex: 1 1 0%; min-width: 90px;">
                                                    @csrf
                                                    <input type="hidden" name="date" value="{{ $appt->fecha_hora->format('Y-m-d H:i:s') }}">
                                                    <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;" onclick="window.showConfirm('¿Seguro querés cancelar este turno proyectado? Podrás recuperarlo luego.', () => this.closest('form').submit())">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <div style="text-align: center;">
                                        @if($appt->estado === \App\Models\Appointment::ESTADO_SESION_PERDIDA)
                                            <span class="neobrutalist-btn disabled-btn" style="font-size: 0.7rem; padding: 0.3rem 0.8rem; opacity: 0.6; cursor: default; background: #ffe4e6; color: #e11d48;"><i class="fa-solid fa-xmark"></i> Perdida</span>
                                        @elseif($appt->recuperada ?? false)
                                            <span class="neobrutalist-btn disabled-btn" style="font-size: 0.7rem; padding: 0.3rem 0.8rem; opacity: 0.6; cursor: default; background: #e0e7ff; color: #4338ca;"><i class="fa-solid fa-check-double"></i> Recuperada</span>
                                        @elseif($appt->id)
                                            <button onclick="openRecoveryModal({{ $appt->id }}, '{{ $appt->fecha_hora->format('Y-m-d H:i') }}')" class="neobrutalist-btn" style="background: var(--color-amarillo); color: #000; font-size: 0.7rem; padding: 0.3rem 0.8rem; border: 2px solid #000; box-shadow: 2px 2px 0px #000;">
                                                <i class="fa-solid fa-rotate-left"></i> Recuperar
                                            </button>
                                        @else
                                            <span class="neobrutalist-btn disabled-btn" style="font-size: 0.7rem; padding: 0.3rem 0.8rem; opacity: 0.6; cursor: default;"><i class="fa-solid fa-clock"></i> Programada</span>
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
                    $canPayThisOne = in_array($appt->id ?? $appt->fecha_hora->timestamp, $payableApptIds);
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
                                    \App\Models\Appointment::ESTADO_FINALIZADO => '#f1f5f9',
                                    default => '#fffbeb'
                                };
                                $statusColor = match($appt->estado) {
                                    'confirmado' => '#166534',
                                    'cancelado' => '#991b1b',
                                    \App\Models\Appointment::ESTADO_SESION_PERDIDA => '#e11d48',
                                    \App\Models\Appointment::ESTADO_FINALIZADO => '#475569',
                                    default => '#92400e'
                                };
                            @endphp
                            <span style="font-weight: 900; color: {{ $statusColor }}; background: {{ $statusBg }}; border: 1px solid {{ $statusColor }}; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; display: inline-block; text-transform: uppercase; white-space: nowrap;">
                                {{ $statusName }}
                            </span>
                            @if($appt->es_recurrente && !($appt->recuperada ?? false))
                                <div style="font-size: 0.55rem; color: #000; font-weight: 900; text-transform: uppercase; display: flex; align-items: center; gap: 3px; white-space: nowrap; margin-top: 4px;">
                                    <i class="fa-solid fa-repeat" style="color: #6366f1;"></i> Fijo
                                </div>
                            @endif
                        </span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Pago:</span>
                        <span class="card-value">
                            @if($appt->payment && $appt->payment->estado == 'verificado')
                                <span style="color: green; font-weight: bold;">Verificado</span>
                            @elseif($appt->payment && $appt->payment->estado == 'pendiente')
                                <span style="color: grey; font-weight: bold;">En Revisión</span>
                            @elseif($appt->payment && $appt->payment->estado == 'rechazado')
                                <span style="color: #e11d48; font-weight: bold;">Rechazado</span>
                            @else
                                <span style="color: #666; font-weight: bold;">Pendiente</span>
                            @endif
                        </span>
                    </div>
                    <div class="actions-wrapper">
                        @if($isFinished)
                            <div style="width: 100%; text-align: center; color: #666; font-weight: 900; padding: 0.5rem;">—</div>
                        @elseif($appt->estado != 'cancelado' && $appt->estado != \App\Models\Appointment::ESTADO_SESION_PERDIDA)
                            @if($isPaid)
                                @if($isVirtual && $appt->estado != 'pendiente' && $appt->estado != 'cancelado')
                                    <a href="{{ $appt->meet_link ?: '#' }}" 
                                       target="_blank" 
                                       class="neobrutalist-btn join-btn {{ $canJoin ? 'bg-celeste' : 'disabled-btn' }}" 
                                       style="flex: 1; padding: 0.3rem 0.6rem; font-size: 0.75rem; border: 2px solid #000; text-decoration: none; color: #000; display: inline-flex; align-items: center; gap: 4px; justify-content: center; min-width: 90px; height: 32px; white-space: nowrap;"
                                       data-start="{{ $appt->fecha_hora->toISOString() }}"
                                       data-paid="true"
                                       data-estado="{{ $appt->estado }}">
                                        <i class="fa-solid fa-video"></i> Unirse
                                    </a>
                                @endif
                            @else
                                @php
                                    $montoAPagar = auth()->user()->paciente->precio_sesion ?? $appt->monto_final;
                                @endphp
                                @if($canPayThisOne)
                                    <button onclick="openPaymentModal({{ abs($appt->id ?? 0) }}, {{ $montoAPagar }})" class="neobrutalist-btn bg-verde" style="flex: 1; padding: 0.3rem 0.6rem; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center; height: 32px;">
                                        <i class="fa-solid fa-dollar-sign"></i> Pagar
                                    </button>
                                @else
                                    <button class="neobrutalist-btn bg-verde disabled-btn" disabled style="flex: 1; padding: 0.3rem 0.6rem; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center; height: 32px;" title="Pagá la sesión anterior primero">
                                        <i class="fa-solid fa-dollar-sign"></i> Pagar
                                    </button>
                                @endif
                            @endif

                            @if($isPaid || !$isCriticalZone)
                                @if($appt->id)
                                    <form action="{{ route('appointments.cancel', abs($appt->id)) }}" method="POST" style="display:inline; flex: 1;">
                                        @csrf
                                        @php
                                            if ($isPaid) {
                                                $cancelMsg = '¿Seguro querés cancelar este turno? Se generará crédito a tu favor.';
                                            } else {
                                                $cancelMsg = '¿Seguro querés cancelar este turno?';
                                            }
                                        @endphp
                                        <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;" onclick="window.showConfirm('{{ $cancelMsg }}', () => this.closest('form').submit())">
                                            Cancelar
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('appointments.cancelProjected') }}" method="POST" style="display:inline; flex: 1;">
                                        @csrf
                                        <input type="hidden" name="date" value="{{ $appt->fecha_hora->format('Y-m-d H:i:s') }}">
                                        <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;" onclick="window.showConfirm('¿Seguro querés cancelar este turno proyectado? Podrás recuperarlo luego.', () => this.closest('form').submit())">
                                            Cancelar
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @else
                            <div style="text-align: center; padding: 0.3rem;">
                                @if($appt->estado === \App\Models\Appointment::ESTADO_SESION_PERDIDA)
                                    <span class="neobrutalist-btn disabled-btn" style="font-size: 0.7rem; padding: 0.3rem 0.8rem; opacity: 0.6; cursor: default; background: #ffe4e6; color: #e11d48;"><i class="fa-solid fa-xmark"></i> Perdida</span>
                                @elseif($appt->recuperada ?? false)
                                    <span class="neobrutalist-btn disabled-btn" style="font-size: 0.7rem; padding: 0.3rem 0.8rem; opacity: 0.6; cursor: default; background: #e0e7ff; color: #4338ca;"><i class="fa-solid fa-check-double"></i> Recuperada</span>
                                @elseif($appt->id)
                                    <button onclick="openRecoveryModal({{ $appt->id }}, '{{ $appt->fecha_hora->format('Y-m-d H:i') }}')" class="neobrutalist-btn" style="background: var(--color-amarillo); color: #000; font-size: 0.7rem; padding: 0.3rem 0.8rem; border: 2px solid #000; box-shadow: 2px 2px 0px #000;">
                                        <i class="fa-solid fa-rotate-left"></i> Recuperar
                                    </button>
                                @else
                                    <span class="neobrutalist-btn disabled-btn" style="font-size: 0.7rem; padding: 0.3rem 0.8rem; opacity: 0.6; cursor: default;"><i class="fa-solid fa-clock"></i> Programada</span>
                                @endif
                            </div>
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
        setInterval(() => {
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

        // Toggle Ver todos / Ver menos con AJAX (usando delegación de eventos)
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.toggle-view-btn');
            if (btn) {
                e.preventDefault();
                const url = btn.getAttribute('href');
                
                // Buscar el container y actualizarlo
                const container = document.getElementById('appointments-table-container');
                if (container) {
                    const loading = document.getElementById('appointments-loading');
                    if (loading) loading.style.display = 'block';
                    container.style.opacity = '0.5';
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        if (loading) loading.style.display = 'none';
                        container.style.opacity = '1';
                        // Scroll to table
                        document.getElementById('mis-turnos')?.scrollIntoView({ behavior: 'smooth' });
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
        });
    </script>

@else
    <div style="flex-grow: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 3rem; border: 3px dashed #000; border-radius: 12px; background: #fafafa; margin: 1rem 0;">
        <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
        <p style="font-weight: 800; color: #000; text-align: center; font-size: 1.1rem; margin-bottom: 1.5rem;">No hay turnos que coincidan con tu búsqueda.</p>
        <a href="{{ route('patient.dashboard') }}" class="neobrutalist-btn bg-amarillo" style="text-decoration: none; color: #000; padding: 0.8rem 1.5rem; font-weight: 800; border: 3px solid #000; box-shadow: 4px 4px 0px #000;">
            <i class="fa-solid fa-list"></i> Ver todos los turnos
        </a>
    </div>
@endif
