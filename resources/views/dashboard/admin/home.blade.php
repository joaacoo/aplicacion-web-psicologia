@extends('layouts.app')

@section('title', 'Inicio - Lic. Nazarena')
@section('header_title', 'Inicio')

@section('content')
<div class="admin-content-wrapper" style="padding: 2rem; max-width: 1400px; margin: 0 auto; margin-bottom: 40px;">
    
    <!-- Welcome Text -->
    <!-- Welcome Text -->
    <div style="margin-bottom: 2rem;">
        <h1 id="bienvenida-text" class="welcome-text" style="font-weight: 700; color: #000; font-family: 'Syne', sans-serif; letter-spacing: -0.5px; text-shadow: 2px 2px 0px rgba(0,0,0,0.1); margin-top: 0;">

            {{ $welcomeMessage }}
        </h1>
        <style>
            .welcome-text {
                font-size: 1.8rem;
            }
            @media (max-width: 768px) {
                .welcome-text {
                    font-size: 1.2rem !important; /* Smaller as requested */
                    line-height: 1.3 !important;
                    margin-bottom: 1rem !important;
                    margin-top: 0 !important;
                }
                /* Target the parent container to reduce padding on mobile */
                .admin-content-wrapper {
                    padding: 1rem !important;
                }
            }
        </style>
    </div>
    
    <!-- Finance Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        
        <!-- Ingresos del Mes Card -->
        <a href="{{ route('admin.finanzas') }}" style="text-decoration: none; color: inherit; display: block; background: var(--color-verde); border: 3px solid #000; border-radius: 12px; padding: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translate(-2px,-2px)'; this.style.boxShadow='10px 10px 0px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='8px 8px 0px rgba(0,0,0,0.1)'">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                <div>
                    <p style="margin: 0; color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Ingresos Mes</p>
                    <h2 style="margin: 0.5rem 0 0 0; font-size: 2rem; font-weight: 900; color: #000; font-family: 'Inter', 'Manrope', monospace; letter-spacing: -1px;">${{ number_format($monthlyIncome, 0, ',', '.') }}</h2>
                </div>
                <div style="background: white; width: 50px; height: 50px; border-radius: 50%; border: 2px solid #000; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-money-bill-wave" style="font-size: 1.2rem; color: #000;"></i>
                </div>
            </div>
            <div style="color: #000; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                Ver finanzas <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Pagos Pendientes Card -->
        <a href="{{ route('admin.finanzas') }}" style="text-decoration: none; color: inherit; display: block; background: var(--color-rosa); border: 3px solid #000; border-radius: 12px; padding: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translate(-2px,-2px)'; this.style.boxShadow='10px 10px 0px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='8px 8px 0px rgba(0,0,0,0.1)'">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                <div>
                    <p style="margin: 0; color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Por Cobrar</p>
                    <h2 style="margin: 0.5rem 0 0 0; font-size: 2rem; font-weight: 900; color: #000; font-family: 'Inter', 'Manrope', monospace; letter-spacing: -1px;">${{ number_format($pendingIncome, 0, ',', '.') }}</h2>
                </div>
                <div style="background: white; width: 50px; height: 50px; border-radius: 50%; border: 2px solid #000; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-hand-holding-dollar" style="font-size: 1.4rem; color: #000;"></i>
                </div>
            </div>
            <div style="color: #000; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                Gestionar cobros <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Pacientes Nuevos Card -->
        <a href="{{ route('admin.pacientes') }}" style="text-decoration: none; color: inherit; display: block; background: var(--color-amarillo); border: 3px solid #000; border-radius: 12px; padding: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translate(-2px,-2px)'; this.style.boxShadow='10px 10px 0px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='8px 8px 0px rgba(0,0,0,0.1)'">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                <div>
                    <p style="margin: 0; color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Pacientes Nuevos</p>
                    <h2 style="margin: 0.5rem 0 0 0; font-size: 2.5rem; font-weight: 900; color: #000; font-family: 'Inter', 'Manrope', monospace; letter-spacing: -2px;">{{ $newPatientsCount }}</h2>
                </div>
                <div style="background: white; width: 50px; height: 50px; border-radius: 50%; border: 2px solid #000; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-user-plus" style="font-size: 1.2rem; color: #000;"></i>
                </div>
            </div>
            <div style="color: #000; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                Ver pacientes <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Sesiones Facturadas Card -->
        <a href="{{ route('admin.agenda') }}" style="text-decoration: none; color: inherit; display: block; background: var(--color-celeste); border: 3px solid #000; border-radius: 12px; padding: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translate(-2px,-2px)'; this.style.boxShadow='10px 10px 0px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='8px 8px 0px rgba(0,0,0,0.1)'">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                <div>
                    <p style="margin: 0; color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Sesiones</p>
                    <h2 style="margin: 0.5rem 0 0 0; font-size: 2.5rem; font-weight: 900; color: #000; font-family: 'Inter', 'Manrope', monospace; letter-spacing: -2px;">{{ $sessionsCount }}</h2>

                </div>
                <div style="background: white; width: 50px; height: 50px; border-radius: 50%; border: 2px solid #000; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-chart-line" style="font-size: 1.2rem; color: #000;"></i>
                </div>
            </div>
            <div style="color: #000; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                Ver agenda <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Next Session Alert -->
    @if(isset($nextAdminAppointment))
        <div style="background: var(--color-lila); border: 3px solid #000; border-radius: 12px; padding: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                <div>
                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 600; color: #000; text-transform: uppercase;">
                        <i class="fa-solid fa-bell" style="color: #ea4335;"></i> Tu próxima sesión
                    </h3>
                    <p style="margin: 0; font-size: 1.3rem; font-weight: 900; color: #000;">
                        {{ $nextAdminAppointment->user->nombre }} {{ $nextAdminAppointment->user->apellido }}
                    </p>
                    <p style="margin: 0.5rem 0 0 0; font-size: 1rem; color: #666;">
                        <i class="fa-solid fa-calendar"></i> {{ $nextAdminAppointment->fecha_hora->locale('es')->isoFormat('dddd D [de] MMMM [a las] H:mm') }} hs
                    </p>
                </div>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="https://meet.google.com/landing" target="_blank" class="neobrutalist-btn" style="background: #ea4335; color: white; border: 2px solid #000; text-decoration: none; display: inline-flex; align-items: center; gap: 0.75rem; padding: 1rem 1.5rem; font-weight: 900; box-shadow: 4px 4px 0px #000;">
                        <i class="fa-solid fa-video"></i> Abrir Google Meet
                    </a>
                    <a href="{{ route('admin.agenda') }}" class="neobrutalist-btn" style="background: white; border: 2px solid #000; text-decoration: none; display: inline-flex; align-items: center; gap: 0.75rem; padding: 1rem 1.5rem; font-weight: 900; box-shadow: 4px 4px 0px #000;">
                        <i class="fa-solid fa-calendar"></i> Ver agenda
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Today's Appointments Table -->
    @if(count($todayAppointments ?? []) > 0)
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
                                <td style="padding: 1rem; font-weight: 900; color: #000;">{{ $appt->fecha_hora->format('H:i') }} hs</td>
                                <td style="padding: 1rem; color: #333;">{{ $appt->user->nombre }} {{ $appt->user->apellido }}</td>
                                <td style="padding: 1rem;">
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
                                <td style="padding: 1rem; text-align: center;">
                                    <a href="https://meet.google.com/landing" target="_blank" style="color: #ea4335; text-decoration: none; font-weight: 700; font-size: 0.9rem;">
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
        <div style="background: white; border: 3px solid #000; border-radius: 12px; padding: 3rem 2rem; text-align: center; margin-bottom: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div style="background: #e6fffa; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; border: 2px solid #000;">
                <i class="fa-solid fa-check" style="font-size: 2.5rem; color: #000;"></i>
            </div>
            <h3 style="margin: 0 0 0.5rem 0; color: #000; font-size: 1.2rem; font-weight: 700; font-family: 'Syne', sans-serif;">Todo al día</h3>
            <p style="margin: 0; color: #666; font-size: 0.95rem; font-weight: 500;">No hay turnos programados para hoy.</p>
        </div>
    @endif
</div>

</div>

<style>
    .neobrutalist-btn {
        box-shadow: 4px 4px 0px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }

    .neobrutalist-btn:hover {
        box-shadow: 2px 2px 0px rgba(0,0,0,0.1);
        transform: translate(2px, 2px);
    }

    @media (max-width: 768px) {
        .neobrutalist-btn {
            width: 100%;
        }
    }
</style>

@endsection
