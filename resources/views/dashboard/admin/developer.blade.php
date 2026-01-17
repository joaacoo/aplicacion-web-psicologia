@extends('layouts.app')

@section('content')
<div style="padding: 2rem; font-family: 'Inter', sans-serif;">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">Panel de Desarrollador</h1>
            <p style="color: #666;">Centro de Control y Monitoreo del Sistema</p>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 0.9rem; color: #999;">Última actualización: {{ now()->format('H:i:s') }}</span>
        </div>
    </div>

    <!-- A. Health Semaphore -->
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem;">Estado del Sistema</h3>
        <div style="display: flex; gap: 1rem;">
            <!-- Indicator -->
            <div style="
                flex: 1;
                padding: 1.5rem;
                border-radius: 12px;
                display: flex;
                align-items: center;
                gap: 1.5rem;
                background: {{ $systemStatus == 'green' ? '#ecfdf5' : ($systemStatus == 'yellow' ? '#fefce8' : '#fef2f2') }};
                border: 2px solid {{ $systemStatus == 'green' ? '#10b981' : ($systemStatus == 'yellow' ? '#eab308' : '#ef4444') }};
            ">
                <div style="
                    width: 60px; height: 60px; border-radius: 50%;
                    background: {{ $systemStatus == 'green' ? '#10b981' : ($systemStatus == 'yellow' ? '#eab308' : '#ef4444') }};
                    display: flex; align-items: center; justify-content: center;
                    font-size: 2rem; color: white;
                ">
                    <i class="fa-solid {{ $systemStatus == 'green' ? 'fa-check' : ($systemStatus == 'yellow' ? 'fa-exclamation' : 'fa-bug') }}"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 1.4rem; font-weight: 800;">
                        {{ $systemStatus == 'green' ? 'SISTEMA ESTABLE' : ($systemStatus == 'yellow' ? 'ATENCIÓN REQUERIDA' : 'ESTADO CRÍTICO') }}
                    </h4>
                    <p style="margin: 0.5rem 0 0 0; color: #555;">
                        {{ $systemStatus == 'green' ? 'Todos los sistemas operativos. No se detectan anomalías.' : 'Se detectaron reportes o errores que requieren revisión.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- B. Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Pacientes</div>
            <div style="font-size: 2.5rem; font-weight: 800; color: #111827; margin: 0.5rem 0;">{{ $totalPatients }}</div>
            <div style="font-size: 0.85rem; color: #10b981;">
                <i class="fa-solid fa-arrow-trend-up"></i> Base de datos activa
            </div>
        </div>

        <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Actividad (Turnos)</div>
            <div style="font-size: 2.5rem; font-weight: 800; color: #111827; margin: 0.5rem 0;">{{ $appointmentsToday }} <span style="font-size: 1.2rem; color: #9ca3af; font-weight: 500;">/ {{ $appointmentsYesterday }}</span></div>
            <div style="font-size: 0.85rem; color: #6b7280;">Hoy vs Ayer</div>
        </div>

        <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Tasa de Error</div>
            <div style="font-size: 2.5rem; font-weight: 800; color: {{ $errorRate > 5 ? '#ef4444' : '#10b981' }}; margin: 0.5rem 0;">{{ $errorRate }}%</div>
            <div style="font-size: 0.85rem; color: #6b7280;">Usuarios afectados</div>
        </div>
    </div>

    <!-- C. Tickets & Logs Split View -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        
        <!-- Tickets Section -->
        <div>
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                Tickets de Soporte
                <span style="background: #f3f4f6; padding: 2px 10px; border-radius: 20px; font-size: 0.8rem;">{{ $tickets->count() }} Total</span>
            </h3>
            
            <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <tr>
                            <th style="padding: 1rem; text-align: left; font-size: 0.85rem; font-weight: 600; color: #6b7280;">Estado</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.85rem; font-weight: 600; color: #6b7280;">Usuario</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.85rem; font-weight: 600; color: #6b7280;">Problema</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.85rem; font-weight: 600; color: #6b7280;">Prioridad</th>
                            <th style="padding: 1rem; text-align: right; font-size: 0.85rem; font-weight: 600; color: #6b7280;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 1rem;">
                                @php
                                    $statusColors = [
                                        'nuevo' => ['bg' => '#fee2e2', 'text' => '#991b1b'], // Red
                                        'en_proceso' => ['bg' => '#fef9c3', 'text' => '#854d0e'], // Yellow
                                        'resuelto' => ['bg' => '#dcfce7', 'text' => '#166534'] // Green
                                    ];
                                    $sc = $statusColors[$ticket->status] ?? $statusColors['nuevo'];
                                @endphp
                                <span style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; padding: 2px 8px; border-radius: 99px; font-size: 0.75rem; font-weight: 600;">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td style="padding: 1rem; font-size: 0.9rem;">
                                {{ $ticket->user ? $ticket->user->nombre : 'Anónimo' }}
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 500; font-size: 0.9rem;">{{ $ticket->subject }}</div>
                                <div style="color: #6b7280; font-size: 0.8rem; margin-top: 2px;">{{ $ticket->created_at->diffForHumans() }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                @php
                                    $prioIcons = [
                                        'critica' => '🔴',
                                        'alta' => '🟠',
                                        'media' => '🟡',
                                        'baja' => '🟢'
                                    ];
                                @endphp
                                {{ $prioIcons[$ticket->priority] ?? '⚪' }} {{ ucfirst($ticket->priority) }}
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <button class="neobrutalist-btn" style="padding: 4px 10px; font-size: 0.8rem;">Ver</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: #9ca3af;">
                                <i class="fa-solid fa-check-circle" style="font-size: 2rem; margin-bottom: 0.5rem; color: #d1d5db;"></i>
                                <p>No hay tickets pendientes</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Console Logs -->
        <div>
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem;">Consola del Sistema</h3>
            <div style="background: #1e1e1e; border-radius: 12px; color: #d4d4d4; font-family: 'Consolas', 'Monaco', monospace; font-size: 0.85rem; overflow: hidden; height: 500px; display: flex; flex-direction: column;">
                <div style="background: #2d2d2d; padding: 0.5rem 1rem; border-bottom: 1px solid #3e3e3e; font-size: 0.8rem; color: #9ca3af;">
                    tail -f /var/log/system.log
                </div>
                <div style="flex: 1; overflow-y: auto; padding: 1rem;">
                    @forelse($logs as $log)
                        <div style="margin-bottom: 0.8rem; border-left: 3px solid {{ $log->level == 'error' ? '#f87171' : '#60a5fa' }}; padding-left: 0.8rem;">
                            <div style="color: #808080; font-size: 0.75rem; margin-bottom: 2px;">
                                [{{ $log->created_at->format('H:i:s') }}] {{ strtoupper($log->level) }}
                            </div>
                            <div style="color: #fff;">{{ $log->message }}</div>
                            @if($log->url)
                                <div style="color: #6b7280; font-size: 0.75rem; margin-top: 2px;">at {{ $log->url }}</div>
                            @endif
                        </div>
                    @empty
                        <div style="color: #6b7280; font-style: italic;">Esperando eventos del sistema...</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
