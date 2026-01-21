@extends('layouts.app')

@section('title', 'Developer - Admin')
@section('header_title', 'Panel de Desarrollador')
@section('content')
<div style="padding: 2rem; font-family: 'Inter', sans-serif;">
    
    <!-- Header -->
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-weight: 700; color: #000; letter-spacing: -1px;">Panel de Desarrollador</h1>
            <p style="color: #666; font-size: 0.9rem;">Monitoreo y herramientas del sistema</p>
        </div>
        <div style="text-align: right;">

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
                    <h4 style="margin: 0; font-size: 1.4rem; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                        <span style="width: 10px; height: 10px; background: {{ $systemStatus == 'green' ? '#10b981' : ($systemStatus == 'yellow' ? '#eab308' : '#ef4444') }}; border-radius: 50%; display: inline-block; animation: pulse 2s infinite;"></span>
                        {{ $systemStatus == 'green' ? 'SISTEMA ESTABLE' : ($systemStatus == 'yellow' ? 'ATENCIÓN REQUERIDA' : 'ESTADO CRÍTICO') }}
                    </h4>
                    <p style="margin: 0.5rem 0 0 0; color: #555;">
                        {{ $systemStatus == 'green' ? 'El sitio de turnos está operando sin problemas. No hay errores reportados.' : 'Se detectaron reportes o errores que requieren revisión.' }}
                    </p>
                    <style>
                    @keyframes pulse {
                        0% { opacity: 1; }
                        50% { opacity: 0.4; }
                        100% { opacity: 1; }
                    }
                    .dev-action-btn {
                       transition: transform 0.2s, background-color 0.2s;
                    }
                    .dev-action-btn:hover {
                        transform: translateY(-3px);
                        background-color: #f9fafb !important;
                        border-color: #d1d5db !important;
                    }
                    </style>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & Services -->
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Acciones de Desarrollador</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 0.5rem;">
            <button onclick="runQuickAction('{{ route('admin.developer.clear-cache') }}')" class="dev-action-btn" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.85rem; font-weight: 600;">
                <i class="fa-solid fa-broom" style="margin-right: 5px;"></i> Limpiar Caché
            </button>
            <button onclick="runQuickAction('{{ route('admin.developer.maintenance-mode') }}')" class="dev-action-btn" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.85rem; font-weight: 600;">
                <i class="fa-solid fa-hammer" style="margin-right: 5px;"></i> Modo Mantenimiento
            </button>
            <button onclick="testAI()" class="dev-action-btn" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: #3b82f6;">
                <i class="fa-solid fa-robot" style="margin-right: 5px;"></i> Testear IA
            </button>
        </div>
        <script>
            function testAI() {
                if(!confirm('¿Probar conexión con Gemini 1.5 Flash?')) return;
                fetch('{{ route("admin.ai.chat") }}', { 
                    method: 'POST', 
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({ message: 'Hola, esto es un test de conexión.' })
                })
                .then(r => r.json())
                .then(d => alert('Respuesta IA: ' + d.response))
                .catch(e => alert('Error: ' + e));
            }
        </script>
        <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-info-circle" style="margin-right: 4px;"></i> 
            <strong>Limpiar Caché:</strong> Elimina archivos temporales para reflejar cambios recientes. 
            <strong>Modo Mantenimiento:</strong> Desactiva el acceso público al sitio temporalmente.
        </p>
        
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Estado Servicios</h3>
        <div style="display: flex; gap: 1rem; margin-bottom: 0.5rem;">
             <div style="display: flex; align-items: center; gap: 5px; font-size: 0.9rem;">
                <span style="width: 8px; height: 8px; background: {{ DB::connection()->getPdo() ? '#10b981' : '#ef4444' }}; border-radius: 50%;"></span> MySQL
             </div>
             <div style="display: flex; align-items: center; gap: 5px; font-size: 0.9rem;">
                <span style="width: 8px; height: 8px; background: {{ config('mail.host') ? '#10b981' : '#eab308' }}; border-radius: 50%;"></span> Correos
             </div>
             <div style="display: flex; align-items: center; gap: 5px; font-size: 0.9rem;">
                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span> Hostinger
             </div>
        </div>
        <p style="font-size: 0.8rem; color: #6b7280;">
            <i class="fa-solid fa-server" style="margin-right: 4px;"></i>
            <strong>MySQL:</strong> Base de datos principal. 
            <strong>Correos:</strong> Servicio SMTP para emails. 
            <strong>Hostinger:</strong> Estado del servidor de hosting.
        </p>
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
</div>

<script>
    function runQuickAction(url) {
        if(!confirm('¿Ejecutar esta acción remota?')) return;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => alert(data.message))
        .catch(err => alert('Error: ' + err));
    }
</script>
@endsection
