@if(isset($appointments) && $appointments->count() > 0)
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
                        <tr style="border-bottom: 1px solid #2D2D2D;">
                            <td style="padding: 0.5rem; white-space: nowrap; text-align: center;">{{ $appt->fecha_hora->format('d/m') }}</td>
                            <td style="padding: 0.5rem; white-space: nowrap; text-align: center;">{{ $appt->fecha_hora->format('H:i') }}</td>
                            <td style="padding: 0.5rem; text-align: center;">
                                {{ ucfirst($appt->modalidad ?? 'Virtual') }}
                            </td>
                            <td style="padding: 0.5rem; white-space: nowrap;">
                                @php
                                    $statusBg = match($appt->estado) {
                                        'confirmado' => '#f0fdf4',
                                        'cancelado' => '#fef2f2',
                                        'realizado' => '#eff6ff',
                                        default => '#fffbeb'
                                    };
                                    $statusColor = match($appt->estado) {
                                        'confirmado' => '#166534',
                                        'cancelado' => '#991b1b',
                                        'realizado' => '#1e40af',
                                        default => '#92400e'
                                    };
                                    $statusBorder = $statusColor;
                                @endphp
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 2px;">
                                    <span style="font-weight: 900; color: {{ $statusColor }}; background: {{ $statusBg }}; border: 1px solid {{ $statusBorder }}; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; display: inline-block; text-transform: uppercase; white-space: nowrap;">
                                        {{ $appt->estado == 'esperando_pago' ? 'Pendiente' : ucfirst($appt->estado) }}
                                    </span>
                                    @if($appt->es_recurrente)
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
                                    <span style="color: #666;">-</span>
                                @endif
                            </td>
                            <td style="padding: 0.5rem;">
                                @if($appt->estado != 'cancelado')
                                    <div class="actions-wrapper" style="display: flex; gap: 5px; flex-wrap: wrap; justify-content: center;">
                                        @if(($appt->modalidad ?? 'virtual') == 'virtual' && $appt->estado == 'confirmado')
                                            <a href="{{ $appt->meet_link ?: ($patient->meet_link ?: '#') }}" target="_blank" class="neobrutalist-btn" style="flex: 1 1 0%; padding: 0.3rem 0.6rem; font-size: 0.75rem; background: #fff; border: 2px solid #000; text-decoration: none; color: #000; display: inline-flex; align-items: center; gap: 4px; justify-content: center; min-width: 90px; height: 32px; white-space: nowrap;">
                                                <i class="fa-solid fa-video"></i> Unirse
                                            </a>
                                        @endif
                                        
                                        @if($appt->estado != 'realizado' && $appt->fecha_hora->isFuture())
                                            <form action="{{ route('appointments.cancel', $appt->id) }}" method="POST" style="display:inline; flex: 1 1 0%; min-width: 90px;">
                                                @csrf
                                                <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;" onclick="window.showConfirm('¿Seguro querés cancelar este turno?', () => this.closest('form').submit())">
                                                    Cancelar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <div class="actions-wrapper" style="display: flex; justify-content: center;">
                                        <button onclick="openRecoveryModal({{ $appt->id }})" class="neobrutalist-btn bg-amarillo" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-redo"></i> Recuperar
                                        </button>
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
                                $statusBg = match($appt->estado) {
                                    'confirmado' => '#f0fdf4',
                                    'cancelado' => '#fef2f2',
                                    'realizado' => '#eff6ff',
                                    default => '#fffbeb'
                                };
                                $statusColor = match($appt->estado) {
                                    'confirmado' => '#166534',
                                    'cancelado' => '#991b1b',
                                    'realizado' => '#1e40af',
                                    default => '#92400e'
                                };
                                $statusBorder = $statusColor;
                            @endphp
                            <span style="font-weight: 900; color: {{ $statusColor }}; background: {{ $statusBg }}; border: 1px solid {{ $statusBorder }}; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; display: inline-block; text-transform: uppercase; white-space: nowrap;">
                                {{ $appt->estado == 'esperando_pago' ? 'Pendiente' : ucfirst($appt->estado) }}
                            </span>
                            @if($appt->es_recurrente)
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
                                <span style="color: #666;">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="actions-wrapper">
                        @if($appt->estado != 'cancelado')
                            @if(($appt->modalidad ?? 'virtual') == 'virtual' && $appt->estado == 'confirmado')
                                <a href="{{ $appt->meet_link ?: ($patient->meet_link ?: '#') }}" target="_blank" class="neobrutalist-btn" style="flex: 1; padding: 0.3rem 0.6rem; font-size: 0.75rem; background: #fff; border: 2px solid #000; text-decoration: none; color: #000; display: inline-flex; align-items: center; gap: 4px; justify-content: center; min-width: 90px; height: 32px; white-space: nowrap;">
                                    <i class="fa-solid fa-video"></i> Unirse
                                </a>
                            @endif
                            
                            @if($appt->estado != 'realizado' && $appt->fecha_hora->isFuture())
                                <form action="{{ route('appointments.cancel', $appt->id) }}" method="POST" style="display:inline; flex: 1;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap;" onclick="window.showConfirm('¿Seguro querés cancelar este turno?', () => this.closest('form').submit())">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                        @else
                            <button onclick="openRecoveryModal({{ $appt->id }})" class="neobrutalist-btn bg-amarillo" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-redo"></i> Recuperar
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $appointments->appends(request()->query())->links() }}
    </div>
@else
    <div style="flex-grow: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 2rem; border: 2px dashed #ccc; border-radius: 8px;">
        <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
        <p style="font-weight: 800; color: #666; text-align: center;">No se encontraron turnos en esta fecha.</p>
    </div>
@endif
