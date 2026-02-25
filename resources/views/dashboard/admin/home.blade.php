@extends('layouts.app')

@section('title', 'Inicio - Lic. Nazarena')
@section('header_title', 'Inicio')

@section('content')
<div class="workspace-container admin-content-padding" style="width: 100%; max-width: 1400px; min-height: 80vh; padding-top: 20px; margin: 0 auto; display: flex; flex-direction: column;">
    <div class="admin-content-wrapper">
        
        <div class="welcome-container" style="overflow: hidden;">
            <h1 id="bienvenida-text" class="welcome-text" style="white-space: nowrap; line-height: 1.2; font-size: 1.8rem;">
                {{ $welcomeMessage }}
            </h1>
            <style>
                @media (max-width: 768px) {
                    #bienvenida-text {
                        white-space: normal !important; /* Allow wrapping on mobile */
                        font-size: 1.5rem !important;   /* Reasonable size for wrapped text */
                        line-height: 1.3 !important;
                        text-align: center;
                    }
                }
            </style>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Only run resizing logic on NON-mobile screens
                    if (window.innerWidth > 768) {
                        const el = document.getElementById('bienvenida-text');
                        if(el) {
                            let currentSize = 28; // approx 1.8rem
                            el.style.fontSize = currentSize + 'px';
                            
                            while (el.scrollWidth > el.clientWidth && currentSize > 12) {
                                currentSize -= 1;
                                el.style.fontSize = currentSize + 'px';
                            }
                        }
                    }
                });
                window.addEventListener('resize', function() {
                    // Re-check on resize
                    if (window.innerWidth > 768) {
                        const el = document.getElementById('bienvenida-text');
                        if(el) {
                            el.style.whiteSpace = 'nowrap'; // Ensure it is nowrap on desktop
                            let currentSize = 28; 
                            el.style.fontSize = currentSize + 'px';
                            while (el.scrollWidth > el.clientWidth && currentSize > 12) {
                                currentSize -= 1;
                                el.style.fontSize = currentSize + 'px';
                            }
                        }
                    } else {
                        // Reset for mobile if user resized down
                        const el = document.getElementById('bienvenida-text');
                        if(el) {
                            el.style.whiteSpace = 'normal';
                            el.style.fontSize = ''; // Use CSS
                        }
                    }
                });
            </script>
        </div>

        <div class="stats-grid">
            
            <a href="{{ route('admin.finanzas') }}" class="stat-card card-verde">
                <div class="card-header">
                    <div>
                        <p class="card-label">Ingresos Mes</p>
                        <h2 class="card-value">${{ number_format($monthlyIncome, 0, ',', '.') }}</h2>
                    </div>
                    <div class="card-icon-circle">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="card-footer">
                    Ver finanzas <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('admin.finanzas') }}#honorarios" class="stat-card card-rosa">
                <div class="card-header">
                    <div>
                        <p class="card-label">Por Cobrar</p>
                        <h2 class="card-value">{{ $pendingIncome }}</h2>
                    </div>
                    <div class="card-icon-circle">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>
                <div class="card-footer">
                    Gestionar cobros <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('admin.pacientes') }}" class="stat-card card-amarillo">
                <div class="card-header">
                    <div>
                        <p class="card-label">Pacientes Nuevos</p>
                        <h2 class="card-value">{{ $newPatientsCount }}</h2>
                    </div>
                    <div class="card-icon-circle">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                </div>
                <div class="card-footer">
                    Ver pacientes <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('admin.agenda') }}" class="stat-card card-celeste">
                <div class="card-header">
                    <div>
                        <p class="card-label">Sesiones</p>
                        <h2 class="card-value">{{ $sessionsCount }}</h2>
                    </div>
                    <div class="card-icon-circle">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="card-footer">
                    Ver agenda <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>
        </div>

        @if(count($todayAppointments ?? []) > 0)
        <!-- Today's Appointments Table (Re-inserted) -->
         <div style="background: white; border: 3px solid #000; border-radius: 12px; padding: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.3rem; font-weight: 700; color: #000; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fa-solid fa-clipboard-list"></i> Turnos de Hoy
            </h3>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f0f0f0; border-bottom: 2px solid #000;">
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-family: 'Manrope', sans-serif;">Hora</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-family: 'Manrope', sans-serif;">Paciente</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-family: 'Manrope', sans-serif;">Estado</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; font-family: 'Manrope', sans-serif;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayAppointments as $appt)
                            <tr style="border-bottom: 1px solid #e0e0e0; transition: background 0.2s;">
                                <td data-label="Hora" style="padding: 1rem; font-weight: 900; color: #000;">{{ $appt->fecha_hora->format('H:i') }} hs</td>
                                <td data-label="Paciente" style="padding: 1rem; color: #333;">{{ $appt->user->nombre }} {{ $appt->user->apellido }}</td>
                                <td data-label="Estado" style="padding: 1rem;">
                                    @php
                                        $statusColor = match($appt->estado ?? 'pendiente') {
                                            'confirmado' => 'var(--color-verde)',
                                            'cancelado' => '#ffcccc',
                                            default => 'var(--color-amarillo)'
                                        };
                                    @endphp
                                    <span style="background: {{ $statusColor }}; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.85rem; font-weight: 700; display: inline-block;">
                                        {{ ucfirst($appt->estado ?? 'Pendiente') }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center; display: flex; justify-content: center; gap: 0.5rem; align-items: center;">
                                    @if(in_array($appt->estado, ['pendiente', 'confirmado']))
                                    @if(isset($appt->is_projected) && $appt->is_projected)
                                        <form id="cancel-form-proj-{{ abs($appt->id) }}" action="{{ route('appointments.cancelProjected') }}" method="POST" style="margin: 0;">
                                        <form id="cancel-form-proj-{{ abs($appt->id) }}" action="{{ route('admin.appointments.cancelProjected') }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="fecha_hora" value="{{ is_string($appt->fecha_hora) ? $appt->fecha_hora : $appt->fecha_hora->format('Y-m-d H:i:s') }}">
                                            <input type="hidden" name="usuario_id" value="{{ $appt->usuario_id }}">
                                            <button type="button" onclick="window.showConfirm('¿Estás seguro de cancelar este turno definitivamente? La paciente no tendrá que abonar nada.', function() { if(typeof window.showProcessing === 'function') window.showProcessing('Cancelando turno...'); document.getElementById('cancel-form-proj-{{ abs($appt->id) }}').submit(); })" style="background: white; color: #dc2626; border: 2px solid #000; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; padding: 0; border-radius: 6px; font-weight: 700; cursor: pointer; box-shadow: 2px 2px 0px #000; font-size: 1.1rem;" title="Cancelar Turno">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form id="cancel-form-{{ $appt->id }}" action="{{ route('admin.appointments.cancel', $appt->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('POST')
                                            <button type="button" onclick="window.showConfirm('¿Estás seguro de cancelar este turno definitivamente? La paciente no tendrá que abonar nada.', function() { if(typeof window.showProcessing === 'function') window.showProcessing('Cancelando turno...'); document.getElementById('cancel-form-{{ $appt->id }}').submit(); })" style="background: white; color: #dc2626; border: 2px solid #000; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; padding: 0; border-radius: 6px; font-weight: 700; cursor: pointer; box-shadow: 2px 2px 0px #000; font-size: 1.1rem;" title="Cancelar Turno">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @endif
                                    <a href="{{ $appt->user->paciente->meet_link ?? 'https://meet.google.com/landing' }}" target="_blank" style="color: #ea4335; text-decoration: none; font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; background: white; border: 2px solid #000; width: 34px; height: 34px; border-radius: 6px; box-shadow: 2px 2px 0px #000;" title="Ingresar a la videollamada">
                                        <i class="fa-solid fa-video"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="all-clear-card">
            <div class="check-icon-wrapper">
                <i class="fa-solid fa-check"></i>
            </div>
            <h3 class="status-title">Todo al día</h3>
            <p class="status-subtitle">No hay turnos programados para hoy.</p>
        </div>
        @endif
    </div>

    <style>
        /* ESPACIADO LATERAL PARA MÓVILES */
        @media (max-width: 768px) {
            .admin-content-padding {
                padding-left: 15px !important;  /* Margen interno izquierdo */
                padding-right: 15px !important; /* Margen interno derecho */
                box-sizing: border-box;         /* Asegura que el padding no rompa el ancho */
            }

            /* Ajuste para que la tarjeta neobrutalista no se vea gigante */
            .neobrutalist-card {
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100% !important;
            }

            /* Si usas tablas que se transforman en cards, esto evita que se peguen */
            .patients-table {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
            }
        }

        /* Estilos Base / Desktop */
        .admin-content-wrapper { padding: 2rem; max-width: 1400px; margin: 0 auto 40px auto; width: 100%; box-sizing: border-box; }
        .welcome-container { margin-bottom: 2rem; text-align: center; }
        .welcome-text { font-weight: 700; color: #000; font-family: 'Syne', sans-serif; font-size: 1.8rem; letter-spacing: -0.5px; margin: 0; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 3rem; }
        
        .stat-card { 
            text-decoration: none; color: inherit; display: flex; flex-direction: column; 
            justify-content: space-between; min-height: 160px; border: 3px solid #000; 
            border-radius: 12px; padding: 1.5rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); 
            transition: transform 0.2s, box-shadow 0.2s; 
        }
        .stat-card:hover { transform: translate(-2px,-2px); box-shadow: 10px 10px 0px rgba(0,0,0,0.1); }
        
        .card-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; }
        .card-label { margin: 0; color: #333; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; }
        .card-value { margin: 0.5rem 0 0 0; font-size: 1.8rem; font-weight: 900; color: #000; font-family: 'Inter', sans-serif; }
        .card-icon-circle { background: white; width: 45px; height: 45px; border-radius: 50%; border: 3px solid #000; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card-footer { color: #000; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.5rem; }

        .card-verde { background: var(--color-verde); }
        .card-rosa { background: var(--color-rosa); }
        .card-amarillo { background: var(--color-amarillo); }
        .card-celeste { background: var(--color-celeste); }

        .all-clear-card { background: white; border: 3px solid #000; border-radius: 12px; padding: 3rem 2rem; text-align: center; margin-bottom: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .check-icon-wrapper { background: #e6fffa; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; border: 2px solid #000; }
        .status-title { margin: 0 0 0.5rem 0; color: #000; font-size: 1.2rem; font-weight: 700; font-family: 'Syne', sans-serif; }
        .status-subtitle { margin: 0; color: #666; font-size: 0.95rem; font-weight: 500; }

        /* Móvil (Breakpoints) */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .admin-content-wrapper { padding: 1rem !important; }
            .welcome-text { font-size: 1.2rem !important; line-height: 1.3; }
            
            /* Scroll Horizontal en móvil para no saturar verticalmente */
            .stats-grid {
                display: flex !important;
                flex-direction: row !important;
                overflow-x: auto !important;
                padding: 0.5rem 0.5rem 1.5rem 0.5rem !important;
                margin: 0 -1rem 2rem -1rem !important; /* Estirar a los bordes */
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
            }
            .stat-card {
                min-width: 260px !important;
                scroll-snap-align: center;
                margin-left: 1rem;
            }
            .stat-card:last-child { margin-right: 1rem; }
            
            .all-clear-card { padding: 2rem 1rem; }
        }
    </style>
</div>

<style>
    .neobrutalist-btn {
        /* box-shadow: 4px 4px 0px rgba(0,0,0,0.1); */
        transition: all 0.2s;
    }

    .neobrutalist-btn:hover {
        /* box-shadow: 2px 2px 0px rgba(0,0,0,0.1); */
        transform: translate(2px, 2px);
    }

    @media (max-width: 768px) {
        /* General Mobile Layout Fixes */
        .workspace-container {
            padding-top: 0 !important;
        }
        
        .admin-content-wrapper {
            padding: 1rem 1rem 2rem 1rem !important;
            margin-bottom: 2rem !important;
        }

        /* Welcome Text Sizing */
        .welcome-text {
            font-size: 1.35rem !important;
            line-height: 1.3 !important;
            margin-bottom: 0.5rem !important;
            text-align: left !important;
        }
        
        /* Mobile: Vertical Stack (One below another) */
        @media (max-width: 1024px) {
            .stats-grid { 
                grid-template-columns: repeat(2, 1fr) !important; 
                gap: 1rem !important;
            }
        }

        @media (max-width: 768px) {
            .admin-content-wrapper { 
                padding: 1rem !important; 
            }
            
            .welcome-text { 
                font-size: 1.2rem !important; 
                line-height: 1.3 !important; 
            }
            
            /* Vertical Stack for Cards */
            .stats-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 1.2rem !important;
                padding-bottom: 2rem !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                overflow-x: visible !important;
            }
            
            .stat-card {
                width: 100% !important;
                min-height: 140px !important;
                flex-shrink: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            
            /* Remove scrollbar styling as it is no longer needed */
            .stats-grid::-webkit-scrollbar {
                display: none;
            }
        }

        /* Compact buttons */
        .neobrutalist-btn {
            width: 100%;
        }
        
        /* Compact 'All Clear' card */
        .all-clear-card {
            padding: 2rem 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        
        /* Remove table borders on mobile */
        table {
            border: none !important;
            box-shadow: none !important;
        }
        
        div[style*="overflow-x: auto"] {
            border: none !important;
            box-shadow: none !important;
            margin-left: -1rem !important;
            margin-right: -1rem !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            width: auto !important;
        }

        /* Ensure neobrutalist cards have margin */
        .neobrutalist-card {
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding: 1.5rem !important;
        }
    }
</style>

@endsection
